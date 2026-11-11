<?php

use App\Models\Category;
use App\Models\ForumReply;
use App\Models\ForumReplyVote;
use App\Models\ForumThread;
use App\Models\User;

beforeEach(function () {
    $this->voter = User::factory()->create(['role' => 'Anggota']);
    $owner = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Voting']);
    $this->thread = ForumThread::create([
        'user_id' => $owner->id, 'category_id' => $category->id,
        'judul' => 'Voting test', 'konten' => 'Discussion', 'status' => 'approved',
    ]);
    $this->reply = ForumReply::create([
        'thread_id' => $this->thread->id, 'user_id' => $owner->id, 'konten' => 'Reply',
    ]);
    $this->url = '/forum/reply/'.$this->reply->id.'/vote';
});

it('persists one vote per user and supports switching and cancelling it', function () {
    $this->actingAs($this->voter)->putJson($this->url, ['value' => 1])
        ->assertOk()->assertExactJson(['upvotes' => 1, 'downvotes' => 0, 'user_vote' => 1]);
    $this->putJson($this->url, ['value' => 1])->assertOk()->assertJsonPath('upvotes', 1);
    expect(ForumReplyVote::count())->toBe(1);
    $this->putJson($this->url, ['value' => -1])->assertOk()
        ->assertExactJson(['upvotes' => 0, 'downvotes' => 1, 'user_vote' => -1]);
    $this->putJson($this->url, ['value' => 0])->assertOk()
        ->assertExactJson(['upvotes' => 0, 'downvotes' => 0, 'user_vote' => 0]);
    expect(ForumReplyVote::count())->toBe(0);
});

it('keeps votes from different users separate and exposes the saved summary', function () {
    $this->actingAs($this->voter)->putJson($this->url, ['value' => 1])->assertOk();
    $other = User::factory()->create(['role' => 'Anggota']);
    $this->actingAs($other)->putJson($this->url, ['value' => -1])->assertOk()
        ->assertExactJson(['upvotes' => 1, 'downvotes' => 1, 'user_vote' => -1]);
    $summary = ForumReply::withVoteSummary()->findOrFail($this->reply->id);
    expect((int) $summary->upvotes)->toBe(1)
        ->and((int) $summary->downvotes)->toBe(1)
        ->and((int) $summary->user_vote)->toBe(-1);
    expect($this->reply->fresh()->updated_at->equalTo($this->reply->updated_at))->toBeTrue();
});

it('rejects guests and invalid votes', function () {
    $this->putJson($this->url, ['value' => 1])->assertUnauthorized();
    $this->actingAs($this->voter)->putJson($this->url, ['value' => 2])->assertUnprocessable();
    $this->putJson($this->url, [])->assertUnprocessable();
    expect(ForumReplyVote::count())->toBe(0);
});

it('rejects votes on locked unpublished deleted threads or deleted replies', function ($state) {
    if ($state === 'locked') {
        $this->thread->update(['is_locked' => true]);
    }
    if ($state === 'pending') {
        $this->thread->update(['status' => 'pending']);
    }
    if ($state === 'rejected') {
        $this->thread->update(['status' => 'rejected']);
    }
    if ($state === 'deleted thread') {
        $this->thread->delete();
    }
    if ($state === 'deleted reply') {
        $this->reply->delete();
    }
    $response = $this->actingAs($this->voter)->putJson($this->url, ['value' => 1]);
    $response->assertStatus($state === 'locked' ? 403 : 404);
    expect(ForumReplyVote::count())->toBe(0);
})->with(['locked', 'pending', 'rejected', 'deleted thread', 'deleted reply']);

it('supports nested replies and loads their vote summary', function () {
    $nested = ForumReply::create([
        'thread_id' => $this->thread->id, 'user_id' => $this->voter->id,
        'parent_id' => $this->reply->id, 'konten' => 'Nested reply',
    ]);
    $this->actingAs($this->voter)->putJson('/forum/reply/'.$nested->id.'/vote', ['value' => 1])->assertOk();
    $loaded = $this->reply->fresh()->replies->first();
    expect((int) $loaded->upvotes)->toBe(1)->and((int) $loaded->user_vote)->toBe(1);
});
