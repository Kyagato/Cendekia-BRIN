<?php

use App\Models\Category;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;

beforeEach(function () {
    $this->category = Category::create([
        'nama_kategori' => 'Kategori Uji Keamanan',
    ]);
});

it('forbids guests and other users from viewing pending or rejected threads', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $otherUser = User::factory()->create(['role' => 'Anggota']);

    $pendingThread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Topik Menunggu Persetujuan',
        'konten' => 'Isi topik menunggu.',
        'status' => 'pending',
    ]);

    $rejectedThread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Topik Ditolak',
        'konten' => 'Isi topik ditolak.',
        'status' => 'rejected',
        'rejection_note' => 'Perlu revisi',
    ]);

    // Guest cannot view pending or rejected thread
    $this->get(route('forum.show', $pendingThread->id))->assertNotFound();
    $this->get(route('forum.show', $rejectedThread->id))->assertNotFound();

    // Other regular user cannot view author's pending or rejected thread
    $this->actingAs($otherUser)->get(route('forum.show', $pendingThread->id))->assertNotFound();
    $this->actingAs($otherUser)->get(route('forum.show', $rejectedThread->id))->assertNotFound();

    // Author CAN view their own pending and rejected thread
    $this->actingAs($author)->get(route('forum.show', $pendingThread->id))->assertOk();
    $this->actingAs($author)->get(route('forum.show', $rejectedThread->id))->assertOk();

    // Moderator CAN view any pending and rejected thread
    $moderator = User::factory()->create(['role' => 'Moderator']);
    $this->actingAs($moderator)->get(route('forum.show', $pendingThread->id))->assertOk();
    $this->actingAs($moderator)->get(route('forum.show', $rejectedThread->id))->assertOk();
});

it('forbids users from replying to pending or rejected threads', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $replier = User::factory()->create(['role' => 'Anggota']);

    $pendingThread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Topik Pending',
        'konten' => 'Isi topik pending.',
        'status' => 'pending',
    ]);

    $rejectedThread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Topik Rejected',
        'konten' => 'Isi topik rejected.',
        'status' => 'rejected',
    ]);

    $approvedThread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Topik Approved',
        'konten' => 'Isi topik approved.',
        'status' => 'approved',
    ]);

    // Attempt reply on pending thread
    $responsePending = $this->actingAs($replier)->post(route('forum.reply', $pendingThread->id), [
        'konten' => 'Mencoba membalas topik pending',
    ]);
    $responsePending->assertSessionHas('error', 'Topik diskusi ini belum disetujui, sehingga belum dapat menerima balasan.');
    expect(ForumReply::where('thread_id', $pendingThread->id)->count())->toBe(0);

    // Attempt reply on rejected thread
    $responseRejected = $this->actingAs($replier)->post(route('forum.reply', $rejectedThread->id), [
        'konten' => 'Mencoba membalas topik rejected',
    ]);
    $responseRejected->assertSessionHas('error', 'Topik diskusi ini belum disetujui, sehingga belum dapat menerima balasan.');
    expect(ForumReply::where('thread_id', $rejectedThread->id)->count())->toBe(0);

    // Reply on approved thread succeeds
    $responseApproved = $this->actingAs($replier)->post(route('forum.reply', $approvedThread->id), [
        'konten' => 'Balasan yang sah pada topik approved',
    ]);
    $responseApproved->assertSessionHas('success', 'Balasan berhasil ditambahkan.');
    expect(ForumReply::where('thread_id', $approvedThread->id)->count())->toBe(1);
});

it('sanitizes malicious html and scripts from thread and reply content', function () {
    $user = User::factory()->create(['role' => 'Anggota']);

    // Create thread with malicious tags
    $this->actingAs($user)->post(route('forum.store'), [
        'judul' => '<script>alert("xss")</script>Judul Aman',
        'konten' => '<p>Halo</p><script>alert("hack")</script><iframe src="evil.com"></iframe>Teks Diskusi',
        'category_id' => $this->category->id,
    ]);

    $thread = ForumThread::latest()->first();
    expect($thread->judul)->not->toContain('<script>');
    expect($thread->judul)->toBe('alert("xss")Judul Aman');
    expect($thread->konten)->not->toContain('<script>');
    expect($thread->konten)->not->toContain('<iframe>');
    expect($thread->konten)->toBe('Haloalert("hack")Teks Diskusi');

    // Create approved thread to test reply sanitization
    $approvedThread = ForumThread::create([
        'user_id' => $user->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Sanitasi',
        'konten' => 'Konten uji.',
        'status' => 'approved',
    ]);

    $this->actingAs($user)->post(route('forum.reply', $approvedThread->id), [
        'konten' => '<script>alert("reply-xss")</script>Balasan teks biasa',
    ]);

    $reply = ForumReply::latest()->first();
    expect($reply->konten)->not->toContain('<script>');
    expect($reply->konten)->toBe('alert("reply-xss")Balasan teks biasa');
});
