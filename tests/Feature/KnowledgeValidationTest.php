<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\User;

it('allows authorized analyst or admin to view validation list and approve knowledge', function () {
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $author = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Tata Kelola SPBE']);

    $knowledge = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Pedoman SPBE Daerah',
        'tipe' => 'Teks',
        'status' => 'Diajukan',
    ]);

    // Akses daftar validasi
    $response = $this->actingAs($admin)->get(route('validasi.index'));
    $response->assertStatus(200);

    // Akses halaman review validasi
    $responseShow = $this->actingAs($admin)->get(route('validasi.show', $knowledge->id));
    $responseShow->assertStatus(200);

    // Approve artikel
    $responseApprove = $this->actingAs($admin)->patch(route('validasi.approve', $knowledge->id));
    $responseApprove->assertSessionHas('success');

    $knowledge->refresh();
    expect($knowledge->status)->toBe('Disetujui');
});

it('allows authorized analyst or admin to reject knowledge with catatan penolakan', function () {
    $analyst = User::factory()->create(['role' => 'Analisis Pengetahuan']);
    $author = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Keamanan Informasi']);

    $knowledge = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Draft Kebijakan Sandi',
        'tipe' => 'Teks',
        'status' => 'Diajukan',
    ]);

    // Reject artikel dengan alasan tolak
    $responseReject = $this->actingAs($analyst)->patch(route('validasi.reject', $knowledge->id), [
        'alasan_tolak' => 'Konten belum melampirkan dasar regulasi yang valid.',
    ]);

    $responseReject->assertRedirect(route('validasi.index'));
    $responseReject->assertSessionHas('success');

    $knowledge->refresh();
    expect($knowledge->status)->toBe('Ditolak');
    expect($knowledge->catatan_penolakan)->toBe('Konten belum melampirkan dasar regulasi yang valid.');
    expect($knowledge->isRejected())->toBeTrue();
});

it('requires alasan tolak when rejecting knowledge', function () {
    $analyst = User::factory()->create(['role' => 'Analisis Pengetahuan']);
    $author = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Keamanan Informasi']);

    $knowledge = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Draft Kebijakan Sandi',
        'tipe' => 'Teks',
        'status' => 'Diajukan',
    ]);

    $response = $this->actingAs($analyst)->patch(route('validasi.reject', $knowledge->id), [
        'alasan_tolak' => '',
    ]);

    $response->assertSessionHasErrors('alasan_tolak');
    $knowledge->refresh();
    expect($knowledge->status)->toBe('Diajukan');
});

it('resubmitting a rejected knowledge by member sets status back to Diajukan and clears catatan penolakan', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Tata Kelola SPBE']);

    $knowledge = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Draft Awal',
        'tipe' => 'Teks',
        'status' => 'Ditolak',
        'catatan_penolakan' => 'Perbaiki bagian pendahuluan dan tambahkan referensi.',
    ]);

    $response = $this->actingAs($author)->put(route('knowledge.update', $knowledge->id), [
        'judul' => 'Draft Setelah Perbaikan',
        'category_id' => $category->id,
        'tipe' => 'Teks',
        'deskripsi' => 'Deskripsi yang sudah diperbaiki',
    ]);

    $response->assertRedirect(route('knowledge.index'));
    $response->assertSessionHas('success');

    $knowledge->refresh();
    expect($knowledge->judul)->toBe('Draft Setelah Perbaikan');
    expect($knowledge->status)->toBe('Diajukan');
    expect($knowledge->catatan_penolakan)->toBeNull();
    expect($knowledge->isSubmitted())->toBeTrue();
});

it('resubmitting a rejected knowledge by admin or analyst sets status directly to Disetujui', function () {
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $category = Category::create(['nama_kategori' => 'Tata Kelola SPBE']);

    $knowledge = Knowledge::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'judul' => 'Draft Ditolak Admin',
        'tipe' => 'Teks',
        'status' => 'Ditolak',
        'catatan_penolakan' => 'Revisi oleh pengawas.',
    ]);

    $response = $this->actingAs($admin)->put(route('knowledge.update', $knowledge->id), [
        'judul' => 'Draft Admin Final',
        'category_id' => $category->id,
        'tipe' => 'Teks',
    ]);

    $response->assertRedirect(route('knowledge.index'));
    $knowledge->refresh();
    expect($knowledge->status)->toBe('Disetujui');
    expect($knowledge->catatan_penolakan)->toBeNull();
    expect($knowledge->isApproved())->toBeTrue();
});

it('prevents regular member from accessing validation routes', function () {
    $member = User::factory()->create(['role' => 'Anggota']);

    $response = $this->actingAs($member)->get(route('validasi.index'));
    $response->assertStatus(403);
});


