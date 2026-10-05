<?php

use App\Models\Category;
use App\Models\ForumThread;
use App\Models\User;

beforeEach(function () {
    $this->category = Category::create([
        'nama_kategori' => 'Kategori Uji Forum',
    ]);
});

it('automatically approves forum threads created by moderators', function () {
    $moderator = User::factory()->create(['role' => 'Moderator']);

    $response = $this->actingAs($moderator)->post(route('forum.store'), [
        'judul' => 'Diskusi Moderasi Resmi',
        'konten' => 'Isi materi diskusi dari moderator.',
        'category_id' => $this->category->id,
    ]);

    $response->assertSessionHas('success', 'Topik diskusi berhasil dibuat dan langsung tayang.');

    $thread = ForumThread::where('judul', 'Diskusi Moderasi Resmi')->first();
    expect($thread)->not->toBeNull();
    expect($thread->status)->toBe('approved');
    expect($thread->approved_by)->toBe($moderator->id);
    expect($thread->approved_at)->not->toBeNull();
});

it('automatically approves forum threads created by admin roles', function (string $role) {
    $admin = User::factory()->create(['role' => $role]);

    $response = $this->actingAs($admin)->post(route('forum.store'), [
        'judul' => "Diskusi Resmi oleh {$role}",
        'konten' => 'Isi materi pengumuman/diskusi admin.',
        'category_id' => $this->category->id,
    ]);

    $response->assertSessionHas('success', 'Topik diskusi berhasil dibuat dan langsung tayang.');

    $thread = ForumThread::where('judul', "Diskusi Resmi oleh {$role}")->first();
    expect($thread)->not->toBeNull();
    expect($thread->status)->toBe('approved');
    expect($thread->approved_by)->toBe($admin->id);
    expect($thread->approved_at)->not->toBeNull();
})->with([
    'Super Admin',
    'Admin Pusat',
    'Admin',
]);

it('requires approval for forum threads created by regular members', function () {
    $member = User::factory()->create(['role' => 'Anggota']);

    $response = $this->actingAs($member)->post(route('forum.store'), [
        'judul' => 'Pertanyaan Member Baru',
        'konten' => 'Mohon pencerahan terkait implementasi domain SPBE.',
        'category_id' => $this->category->id,
    ]);

    $response->assertSessionHas('success', 'Topik diskusi berhasil dibuat dan menunggu persetujuan moderator.');

    $thread = ForumThread::where('judul', 'Pertanyaan Member Baru')->first();
    expect($thread)->not->toBeNull();
    expect($thread->status)->toBe('pending');
    expect($thread->approved_by)->toBeNull();
    expect($thread->approved_at)->toBeNull();
});
