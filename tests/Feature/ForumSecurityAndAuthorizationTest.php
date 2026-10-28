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

it('allows author to edit their own reply and sanitizes content', function () {
    $author = User::factory()->create(['role' => 'Anggota']);

    $thread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Edit Reply',
        'konten' => 'Konten topik.',
        'status' => 'approved',
    ]);

    $reply = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'konten' => 'Balasan awal',
    ]);

    $response = $this->actingAs($author)->put(route('forum.reply.update', $reply->id), [
        'konten' => 'Balasan yang diperbarui<script>alert("hack")</script>',
    ]);

    $response->assertSessionHas('success');
    expect($reply->fresh()->konten)->toBe('Balasan yang diperbaruialert("hack")');
});

it('forbids other users from editing someone else\'s reply', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $otherUser = User::factory()->create(['role' => 'Anggota']);

    $thread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Forbidden Edit Reply',
        'konten' => 'Konten topik.',
        'status' => 'approved',
    ]);

    $reply = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'konten' => 'Balasan orisinal',
    ]);

    $response = $this->actingAs($otherUser)->put(route('forum.reply.update', $reply->id), [
        'konten' => 'Mencoba mengedit balasan orang lain',
    ]);

    $response->assertStatus(403);
    expect($reply->fresh()->konten)->toBe('Balasan orisinal');
});

it('allows author to delete their own reply', function () {
    $author = User::factory()->create(['role' => 'Anggota']);

    $thread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Hapus Reply Sendiri',
        'konten' => 'Konten topik.',
        'status' => 'approved',
    ]);

    $reply = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'konten' => 'Balasan akan dihapus',
    ]);

    $response = $this->actingAs($author)->delete(route('forum.reply.destroy', $reply->id));
    $response->assertSessionHas('success');
    expect(ForumReply::find($reply->id))->toBeNull();
});

it('forbids other regular users from deleting someone else\'s reply', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $otherUser = User::factory()->create(['role' => 'Anggota']);

    $thread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Forbidden Delete Reply',
        'konten' => 'Konten topik.',
        'status' => 'approved',
    ]);

    $reply = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'konten' => 'Balasan jangan dihapus',
    ]);

    $response = $this->actingAs($otherUser)->delete(route('forum.reply.destroy', $reply->id));
    $response->assertStatus(403);
    expect(ForumReply::find($reply->id))->not->toBeNull();
});

it('allows 3 admin roles and moderator to delete any reply', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $adminPusat = User::factory()->create(['role' => 'Admin Pusat']);
    $adminRegular = User::factory()->create(['role' => 'Admin']);
    $moderator = User::factory()->create(['role' => 'Moderator']);
    $analyst = User::factory()->create(['role' => 'Analis Pengetahuan']);

    $thread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Moderasi Hapus Reply',
        'konten' => 'Konten topik.',
        'status' => 'approved',
    ]);

    // Analyst cannot delete
    $reply1 = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'konten' => 'Balasan 1',
    ]);
    $this->actingAs($analyst)->delete(route('forum.reply.destroy', $reply1->id))->assertStatus(403);

    // Moderator CAN delete
    $this->actingAs($moderator)->delete(route('forum.reply.destroy', $reply1->id))->assertSessionHas('success');
    expect(ForumReply::find($reply1->id))->toBeNull();

    // Admin Regular CAN delete
    $reply2 = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'konten' => 'Balasan 2',
    ]);
    $this->actingAs($adminRegular)->delete(route('forum.reply.destroy', $reply2->id))->assertSessionHas('success');
    expect(ForumReply::find($reply2->id))->toBeNull();

    // Admin Pusat CAN delete
    $reply3 = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'konten' => 'Balasan 3',
    ]);
    $this->actingAs($adminPusat)->delete(route('forum.reply.destroy', $reply3->id))->assertSessionHas('success');
    expect(ForumReply::find($reply3->id))->toBeNull();

    // Super Admin CAN delete
    $reply4 = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'konten' => 'Balasan 4',
    ]);
    $this->actingAs($superAdmin)->delete(route('forum.reply.destroy', $reply4->id))->assertSessionHas('success');
    expect(ForumReply::find($reply4->id))->toBeNull();
});

it('allows forum thread creator to pin and unpin any reply in their thread', function () {
    $threadOwner = User::factory()->create(['role' => 'Anggota']);
    $otherMember = User::factory()->create(['role' => 'Anggota']);

    $thread = ForumThread::create([
        'user_id' => $threadOwner->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Pin Komentar Thread Owner',
        'konten' => 'Konten topik.',
        'status' => 'approved',
    ]);

    $reply = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $otherMember->id,
        'konten' => 'Komentar penting dari member lain',
        'is_pinned' => false,
    ]);

    // Pembuat forum bisa pin
    $response = $this->actingAs($threadOwner)->patch(route('forum.reply.pin', $reply->id));
    $response->assertSessionHas('success');
    expect($reply->fresh()->is_pinned)->toBeTrue();

    // Pembuat forum bisa unpin
    $response2 = $this->actingAs($threadOwner)->patch(route('forum.reply.pin', $reply->id));
    $response2->assertSessionHas('success');
    expect($reply->fresh()->is_pinned)->toBeFalse();
});

it('forbids other regular users from pinning replies if they are not the thread creator', function () {
    $threadOwner = User::factory()->create(['role' => 'Anggota']);
    $otherMember1 = User::factory()->create(['role' => 'Anggota']);
    $otherMember2 = User::factory()->create(['role' => 'Anggota']);

    $thread = ForumThread::create([
        'user_id' => $threadOwner->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Forbidden Pin Komentar',
        'konten' => 'Konten topik.',
        'status' => 'approved',
    ]);

    // Komentar milik otherMember1
    $reply = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $otherMember1->id,
        'konten' => 'Komentar saya sendiri tapi saya bukan pembuat thread',
        'is_pinned' => false,
    ]);

    // otherMember1 (pemilik komentar) TIDAK BISA pin karena bukan pembuat thread
    $this->actingAs($otherMember1)->patch(route('forum.reply.pin', $reply->id))->assertStatus(403);
    expect($reply->fresh()->is_pinned)->toBeFalse();

    // otherMember2 juga TIDAK BISA pin
    $this->actingAs($otherMember2)->patch(route('forum.reply.pin', $reply->id))->assertStatus(403);
    expect($reply->fresh()->is_pinned)->toBeFalse();
});

it('allows 3 admin roles and moderator to pin and unpin any reply in any thread', function () {
    $threadOwner = User::factory()->create(['role' => 'Anggota']);
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $adminPusat = User::factory()->create(['role' => 'Admin Pusat']);
    $adminRegular = User::factory()->create(['role' => 'Admin']);
    $moderator = User::factory()->create(['role' => 'Moderator']);
    $analyst = User::factory()->create(['role' => 'Analis Pengetahuan']);

    $thread = ForumThread::create([
        'user_id' => $threadOwner->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Moderasi Pin Reply',
        'konten' => 'Konten topik.',
        'status' => 'approved',
    ]);

    $reply = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $threadOwner->id,
        'konten' => 'Komentar yang akan dipin staf moderasi',
        'is_pinned' => false,
    ]);

    // Analyst cannot pin
    $this->actingAs($analyst)->patch(route('forum.reply.pin', $reply->id))->assertStatus(403);
    expect($reply->fresh()->is_pinned)->toBeFalse();

    // Moderator can pin
    $this->actingAs($moderator)->patch(route('forum.reply.pin', $reply->id))->assertSessionHas('success');
    expect($reply->fresh()->is_pinned)->toBeTrue();

    // Admin Regular can unpin
    $this->actingAs($adminRegular)->patch(route('forum.reply.pin', $reply->id))->assertSessionHas('success');
    expect($reply->fresh()->is_pinned)->toBeFalse();

    // Admin Pusat can pin
    $this->actingAs($adminPusat)->patch(route('forum.reply.pin', $reply->id))->assertSessionHas('success');
    expect($reply->fresh()->is_pinned)->toBeTrue();

    // Super Admin can unpin
    $this->actingAs($superAdmin)->patch(route('forum.reply.pin', $reply->id))->assertSessionHas('success');
    expect($reply->fresh()->is_pinned)->toBeFalse();
});

it('rejects pinning nested replies and only allows top-level replies to be pinned', function () {
    $threadOwner = User::factory()->create(['role' => 'Anggota']);

    $thread = ForumThread::create([
        'user_id' => $threadOwner->id,
        'category_id' => $this->category->id,
        'judul' => 'Diskusi Pin Nested Reply Reject',
        'konten' => 'Konten topik.',
        'status' => 'approved',
    ]);

    $mainReply = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $threadOwner->id,
        'konten' => 'Komentar Utama',
        'parent_id' => null,
        'is_pinned' => false,
    ]);

    $nestedReply = ForumReply::create([
        'thread_id' => $thread->id,
        'user_id' => $threadOwner->id,
        'konten' => 'Balasan di komentar utama',
        'parent_id' => $mainReply->id,
        'is_pinned' => false,
    ]);

    // Coba pin nested reply -> ditolak
    $response = $this->actingAs($threadOwner)->patch(route('forum.reply.pin', $nestedReply->id));
    $response->assertSessionHas('error', 'Hanya komentar utama yang dapat disematkan.');
    expect($nestedReply->fresh()->is_pinned)->toBeFalse();

    // Pin komentar utama -> sukses
    $response2 = $this->actingAs($threadOwner)->patch(route('forum.reply.pin', $mainReply->id));
    $response2->assertSessionHas('success');
    expect($mainReply->fresh()->is_pinned)->toBeTrue();
});
