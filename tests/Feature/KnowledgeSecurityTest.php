<?php

use App\Models\Category;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\Knowledge;
use App\Models\KnowledgeComment;
use App\Models\User;

beforeEach(function () {
    $this->category = Category::firstOrCreate(['nama_kategori' => 'Keamanan Sistem']);
});

it('sanitizes XSS payloads in deskripsi and detail when creating knowledge', function () {
    $admin = User::factory()->create(['role' => 'Super Admin']);

    $response = $this->actingAs($admin)->post(route('knowledge.store'), [
        'judul'        => 'Artikel Uji Sanitasi XSS',
        'category_id'  => $this->category->id,
        'tipe'         => 'Teks',
        'deskripsi'    => '<p>Ringkasan aman</p><script>alert("xss-deskripsi")</script>',
        'detail'       => '<div onmouseover="alert(\'xss-detail\')">Detail info <iframe src="https://evil.com"></iframe></div>',
        'url_teks'     => 'https://example.com/artikel',
        'penulis'      => 'Penulis Uji',
        'status'       => 'Disetujui',
    ]);

    $knowledge = Knowledge::where('judul', 'Artikel Uji Sanitasi XSS')->first();
    expect($knowledge)->not->toBeNull();
    expect($knowledge->deskripsi)->not->toContain('<script');
    expect($knowledge->deskripsi)->not->toContain('alert');
    expect($knowledge->deskripsi)->toContain('Ringkasan aman');

    expect($knowledge->detail)->not->toContain('onmouseover');
    expect($knowledge->detail)->not->toContain('<iframe');
    expect($knowledge->detail)->toContain('Detail info');
});

it('sanitizes XSS payloads in deskripsi and detail when updating knowledge', function () {
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $knowledge = Knowledge::create([
        'user_id'     => $admin->id,
        'category_id' => $this->category->id,
        'judul'       => 'Artikel Asli',
        'tipe'        => 'Teks',
        'deskripsi'   => 'Awal',
        'detail'      => 'Awal',
        'status'      => 'Disetujui',
    ]);

    $response = $this->actingAs($admin)->put(route('knowledge.update', $knowledge->id), [
        'judul'        => 'Artikel Diperbarui',
        'category_id'  => $this->category->id,
        'tipe'         => 'Teks',
        'deskripsi'    => '<p>Update <a href="javascript:alert(1)">Klik Ini</a></p>',
        'detail'       => '<p onclick="alert(2)">Detail Aman</p>',
        'url_teks'     => 'https://example.com/update',
        'status'       => 'Diajukan',
    ]);

    $knowledge->refresh();
    expect($knowledge->deskripsi)->not->toContain('javascript:');
    expect($knowledge->deskripsi)->toContain('Klik Ini');
    expect($knowledge->detail)->not->toContain('onclick');
    expect($knowledge->detail)->toContain('Detail Aman');
});

it('rejects knowledge comment if parent_id belongs to a different knowledge article', function () {
    $user = User::factory()->create();

    $knowledgeA = Knowledge::create([
        'user_id'     => $user->id,
        'category_id' => $this->category->id,
        'judul'       => 'Artikel A',
        'tipe'        => 'Teks',
        'status'      => 'Disetujui',
    ]);

    $knowledgeB = Knowledge::create([
        'user_id'     => $user->id,
        'category_id' => $this->category->id,
        'judul'       => 'Artikel B',
        'tipe'        => 'Teks',
        'status'      => 'Disetujui',
    ]);

    // Buat komentar pada Artikel A
    $commentA = KnowledgeComment::create([
        'knowledge_id' => $knowledgeA->id,
        'user_id'      => $user->id,
        'konten'       => 'Komentar pada artikel A',
    ]);

    // Kirim balasan ke Artikel B tetapi menggunakan parent_id milik Artikel A
    $response = $this->actingAs($user)->post(route('knowledge.comment', $knowledgeB->id), [
        'konten'    => 'Mencoba cross-parent comment',
        'parent_id' => $commentA->id,
    ]);

    $response->assertSessionHasErrors(['parent_id']);
    expect(KnowledgeComment::where('knowledge_id', $knowledgeB->id)->count())->toBe(0);
});

it('rejects forum reply if parent_id belongs to a different thread', function () {
    $user = User::factory()->create();

    $threadA = ForumThread::create([
        'user_id'     => $user->id,
        'category_id' => $this->category->id,
        'judul'       => 'Topik Forum A',
        'konten'      => 'Konten topik A',
        'status'      => ForumThread::STATUS_APPROVED,
    ]);

    $threadB = ForumThread::create([
        'user_id'     => $user->id,
        'category_id' => $this->category->id,
        'judul'       => 'Topik Forum B',
        'konten'      => 'Konten topik B',
        'status'      => ForumThread::STATUS_APPROVED,
    ]);

    // Buat balasan pada Thread A
    $replyA = ForumReply::create([
        'thread_id' => $threadA->id,
        'user_id'   => $user->id,
        'konten'    => 'Balasan di Thread A',
    ]);

    // Kirim balasan ke Thread B tetapi menggunakan parent_id milik Thread A
    $response = $this->actingAs($user)->post(route('forum.reply', $threadB->id), [
        'konten'    => 'Mencoba cross-parent reply',
        'parent_id' => $replyA->id,
    ]);

    $response->assertSessionHasErrors(['parent_id']);
    expect(ForumReply::where('thread_id', $threadB->id)->count())->toBe(0);
});
