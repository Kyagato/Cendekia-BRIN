<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\KnowledgeComment;
use App\Models\KnowledgeCommentVote;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->create(['role' => 'Anggota']);
    $this->voter = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Comment voting']);
    $this->knowledge = Knowledge::create([
        'user_id' => $this->owner->id, 'category_id' => $category->id,
        'judul' => 'Comment voting', 'tipe' => 'Teks',
        'status' => 'Disetujui', 'status_akses' => 'public',
    ]);
    $this->comment = KnowledgeComment::create([
        'knowledge_id' => $this->knowledge->id, 'user_id' => $this->owner->id, 'konten' => 'Comment',
    ]);
    $this->url = '/knowledge/comment/'.$this->comment->id.'/vote';
});

it('persists switches cancels and does not duplicate votes or edit the comment timestamp', function () {
    $this->actingAs($this->voter)->putJson($this->url, ['value' => 1])->assertOk()
        ->assertExactJson(['upvotes' => 1, 'downvotes' => 0, 'user_vote' => 1]);
    $this->putJson($this->url, ['value' => 1])->assertOk()->assertJsonPath('upvotes', 1);
    expect(KnowledgeCommentVote::count())->toBe(1);
    $this->putJson($this->url, ['value' => -1])->assertOk()
        ->assertExactJson(['upvotes' => 0, 'downvotes' => 1, 'user_vote' => -1]);
    $this->putJson($this->url, ['value' => 0])->assertOk()
        ->assertExactJson(['upvotes' => 0, 'downvotes' => 0, 'user_vote' => 0]);
    expect(KnowledgeCommentVote::count())->toBe(0)
        ->and($this->comment->fresh()->updated_at->equalTo($this->comment->updated_at))->toBeTrue();
});

it('keeps different users votes separate', function () {
    $this->actingAs($this->voter)->putJson($this->url, ['value' => 1])->assertOk();
    $this->actingAs($this->owner)->putJson($this->url, ['value' => -1])->assertOk()
        ->assertExactJson(['upvotes' => 1, 'downvotes' => 1, 'user_vote' => -1]);
});

it('rejects guests invalid values and deleted comments', function () {
    $this->putJson($this->url, ['value' => 1])->assertUnauthorized();
    $this->actingAs($this->voter)->putJson($this->url, ['value' => 2])->assertUnprocessable();
    $this->putJson($this->url, [])->assertUnprocessable();
    $this->comment->delete();
    $this->putJson($this->url, ['value' => 1])->assertNotFound();
    expect(KnowledgeCommentVote::count())->toBe(0);
});

it('enforces private draft and deleted knowledge access', function ($state) {
    if ($state === 'private') {
        $this->knowledge->update(['status_akses' => 'private']);
    }
    if ($state === 'draft') {
        $this->knowledge->update(['status' => 'Draft']);
    }
    if ($state === 'deleted') {
        $this->knowledge->delete();
    }
    $this->actingAs($this->voter)->putJson($this->url, ['value' => 1])->assertNotFound();
    if ($state !== 'deleted') {
        $this->actingAs($this->owner)->putJson($this->url, ['value' => 1])->assertOk();
    }
})->with(['private', 'draft', 'deleted']);

it('supports nested comments and loads saved vote summaries', function () {
    $reply = KnowledgeComment::create([
        'knowledge_id' => $this->knowledge->id, 'parent_id' => $this->comment->id,
        'user_id' => $this->voter->id, 'konten' => 'Reply',
    ]);
    $this->actingAs($this->voter)->putJson('/knowledge/comment/'.$reply->id.'/vote', ['value' => 1])->assertOk();
    $loaded = $this->comment->fresh()->replies->first();
    expect((int) $loaded->upvotes)->toBe(1)->and((int) $loaded->user_vote)->toBe(1);
});

it('orders root comments by votes then replies or by date', function ($sort, $keys) {
    $this->comment->delete();
    $make = fn ($date, $content) => KnowledgeComment::create([
        'knowledge_id' => $this->knowledge->id, 'user_id' => $this->owner->id,
        'konten' => $content, 'created_at' => $date,
    ]);
    $items = [
        'oldest' => $make('2026-01-01', 'Oldest'),
        'popular' => $make('2026-02-01', 'Popular'),
        'discussed' => $make('2026-03-01', 'Discussed'),
        'newest' => $make('2026-04-01', 'Newest'),
    ];
    KnowledgeCommentVote::create([
        'knowledge_comment_id' => $items['popular']->id, 'user_id' => $this->voter->id, 'value' => 1,
    ]);
    KnowledgeComment::create([
        'knowledge_id' => $this->knowledge->id, 'user_id' => $this->owner->id,
        'parent_id' => $items['discussed']->id, 'konten' => 'Nested',
    ]);
    $expected = array_map(fn ($key) => $items[$key]->id, $keys);
    $actual = $this->knowledge->comments()->sortedForDisplay($sort)->get()->modelKeys();
    expect($actual)->toBe($expected);
    $response = $this->actingAs($this->voter)->get('/knowledge/'.$this->knowledge->id.'?comment_sort='.$sort)
        ->assertOk()->assertSee('knowledge-comment-sort', false)->assertSee('aria-label="Upvote"', false);
    $response->assertViewHas('comments', fn ($comments) => $comments->modelKeys() === $expected);
})->with([
    ['default', ['popular', 'discussed', 'newest', 'oldest']],
    ['newest', ['newest', 'discussed', 'popular', 'oldest']],
    ['oldest', ['oldest', 'popular', 'discussed', 'newest']],
    ['invalid', ['popular', 'discussed', 'newest', 'oldest']],
]);
