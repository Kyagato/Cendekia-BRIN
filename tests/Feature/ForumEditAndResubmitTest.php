<?php

use App\Models\Category;
use App\Models\ForumThread;
use App\Models\User;

beforeEach(function () {
    $this->category = Category::create([
        'nama_kategori' => 'Kategori Edit Uji',
    ]);
});

it('allows the author to access the edit page of their forum thread', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $thread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Topik Awal',
        'konten' => 'Isi topik awal.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($author)->get(route('forum.edit', $thread->id));
    $response->assertStatus(200);
});

it('forbids other regular users from editing someone elses forum thread', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $other = User::factory()->create(['role' => 'Anggota']);
    $thread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Topik Pribadi',
        'konten' => 'Isi topik.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($other)->get(route('forum.edit', $thread->id));
    $response->assertStatus(403);

    $responseUpdate = $this->actingAs($other)->put(route('forum.update', $thread->id), [
        'judul' => 'Pembajakan Topik',
        'konten' => 'Isi dibajak.',
        'category_id' => $this->category->id,
    ]);
    $responseUpdate->assertStatus(403);
});

it('allows the author to update their forum thread content', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $thread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Judul Sebelum Diedit',
        'konten' => 'Konten sebelum diedit.',
        'status' => 'approved',
    ]);

    $response = $this->actingAs($author)->put(route('forum.update', $thread->id), [
        'judul' => 'Judul Setelah Diedit',
        'konten' => 'Konten setelah diedit dan disempurnakan.',
        'category_id' => $this->category->id,
    ]);

    $response->assertSessionHas('success');

    $thread->refresh();
    expect($thread->judul)->toBe('Judul Setelah Diedit');
    expect($thread->konten)->toBe('Konten setelah diedit dan disempurnakan.');
});

it('resubmitting a rejected thread sets its status back to pending and clears rejection note', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $thread = ForumThread::create([
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'judul' => 'Topik yang Ditolak',
        'konten' => 'Isi materi kurang lengkap.',
        'status' => 'rejected',
        'rejection_note' => 'Mohon sertakan referensi regulasi yang jelas.',
    ]);

    $response = $this->actingAs($author)->put(route('forum.update', $thread->id), [
        'judul' => 'Topik yang Ditolak (Sudah Diperbaiki)',
        'konten' => 'Isi materi kini sudah lengkap dengan referensi Perpres SPBE.',
        'category_id' => $this->category->id,
        'resubmit' => 1,
    ]);

    $response->assertSessionHas('success', 'Topik diskusi berhasil diajukan kembali dan kini berstatus menunggu persetujuan moderator.');

    $thread->refresh();
    expect($thread->judul)->toBe('Topik yang Ditolak (Sudah Diperbaiki)');
    expect($thread->status)->toBe('pending');
    expect($thread->rejection_note)->toBeNull();
    expect($thread->approved_by)->toBeNull();
    expect($thread->approved_at)->toBeNull();
});

it('resubmitting a rejected thread by moderator or admin sets status to approved directly', function () {
    $moderator = User::factory()->create(['role' => 'Moderator']);
    $thread = ForumThread::create([
        'user_id' => $moderator->id,
        'category_id' => $this->category->id,
        'judul' => 'Topik Moderator yang Ditolak',
        'konten' => 'Konten revisi moderator.',
        'status' => 'rejected',
        'rejection_note' => 'Ada typo.',
    ]);

    $response = $this->actingAs($moderator)->put(route('forum.update', $thread->id), [
        'judul' => 'Topik Moderator yang Ditolak (Revisi)',
        'konten' => 'Konten revisi moderator sudah rapi.',
        'category_id' => $this->category->id,
        'resubmit' => 1,
    ]);

    $response->assertSessionHas('success', 'Topik diskusi berhasil diperbaiki dan langsung tayang.');

    $thread->refresh();
    expect($thread->status)->toBe('approved');
    expect($thread->rejection_note)->toBeNull();
    expect($thread->approved_by)->toBe($moderator->id);
    expect($thread->approved_at)->not->toBeNull();
});
