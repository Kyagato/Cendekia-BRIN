<?php

use App\Models\Category;
use App\Models\ForumReply;
use App\Models\ForumReplyVote;
use App\Models\ForumThread;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $user = User::factory()->create();
    $category = Category::create(['nama_kategori' => 'Reply sorting']);
    $this->thread = ForumThread::create([
        'user_id' => $user->id, 'category_id' => $category->id,
        'judul' => 'Sorting', 'konten' => 'Discussion', 'status' => 'approved',
    ]);
    $create = fn ($date) => ForumReply::create([
        'thread_id' => $this->thread->id, 'user_id' => $user->id,
        'konten' => 'Reply', 'created_at' => $date,
    ]);
    $this->oldest = $create('2026-01-01');
    $this->popular = $create('2026-02-01');
    $this->discussed = $create('2026-03-01');
    $this->newest = $create('2026-04-01');
    ForumReplyVote::create(['forum_reply_id' => $this->popular->id, 'user_id' => $user->id, 'value' => 1]);
    ForumReply::create([
        'thread_id' => $this->thread->id, 'parent_id' => $this->discussed->id,
        'user_id' => $user->id, 'konten' => 'Nested reply',
    ]);
});

it('sorts all top level replies using the selected order', function ($sort, $keys) {
    $expected = array_map(fn ($key) => $this->{$key}->id, $keys);
    $this->get('/forum/'.$this->thread->id.'?reply_sort='.$sort)
        ->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Forum/Show')->where('replySort', $sort)
            ->has('replies.data', 4)
            ->where('replies.data.0.id', $expected[0])
            ->where('replies.data.1.id', $expected[1])
            ->where('replies.data.2.id', $expected[2])
            ->where('replies.data.3.id', $expected[3]));
})->with([
    ['default', ['popular', 'discussed', 'newest', 'oldest']],
    ['newest', ['newest', 'discussed', 'popular', 'oldest']],
    ['oldest', ['oldest', 'popular', 'discussed', 'newest']],
]);

it('keeps pinned replies first in every sort order', function ($sort) {
    $this->oldest->update(['is_pinned' => true]);
    $this->get('/forum/'.$this->thread->id.'?reply_sort='.$sort)
        ->assertOk()->assertInertia(fn (Assert $page) => $page->where('replies.data.0.id', $this->oldest->id));
})->with(['default', 'newest', 'oldest']);

it('falls back to default for invalid sort input', function () {
    $this->get('/forum/'.$this->thread->id.'?reply_sort=invalid')
        ->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('replySort', 'default')->where('replies.data.0.id', $this->popular->id));
});
