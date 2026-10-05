<?php

use App\Models\Category;
use App\Models\ForumThread;
use App\Models\User;

test('forum thread has expected status constants', function () {
    expect(ForumThread::STATUS_APPROVED)->toBe('approved');
    expect(ForumThread::STATUS_PENDING)->toBe('pending');
    expect(ForumThread::STATUS_REJECTED)->toBe('rejected');
    expect(ForumThread::ALL_STATUSES)->toBe(['approved', 'pending', 'rejected']);
});

test('forum thread helper methods work correctly', function () {
    $thread = new ForumThread(['status' => ForumThread::STATUS_APPROVED]);
    expect($thread->isApproved())->toBeTrue();
    expect($thread->isPending())->toBeFalse();
    expect($thread->isRejected())->toBeFalse();

    $thread->status = ForumThread::STATUS_PENDING;
    expect($thread->isApproved())->toBeFalse();
    expect($thread->isPending())->toBeTrue();
    expect($thread->isRejected())->toBeFalse();

    $thread->status = ForumThread::STATUS_REJECTED;
    expect($thread->isApproved())->toBeFalse();
    expect($thread->isPending())->toBeFalse();
    expect($thread->isRejected())->toBeTrue();
});

test('forum thread status scopes filter query results properly', function () {
    $user = User::factory()->create();
    $category = Category::create(['nama_kategori' => 'Kategori Unit Test']);

    ForumThread::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'Thread Approved',
        'konten' => 'Konten.',
        'status' => ForumThread::STATUS_APPROVED,
    ]);

    ForumThread::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'Thread Pending',
        'konten' => 'Konten.',
        'status' => ForumThread::STATUS_PENDING,
    ]);

    ForumThread::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'Thread Rejected',
        'konten' => 'Konten.',
        'status' => ForumThread::STATUS_REJECTED,
    ]);

    expect(ForumThread::approved()->count())->toBe(1);
    expect(ForumThread::pending()->count())->toBe(1);
    expect(ForumThread::rejected()->count())->toBe(1);
});
