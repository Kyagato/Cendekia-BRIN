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

it('allows authorized analyst or admin to reject knowledge', function () {
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

    // Reject artikel
    $responseReject = $this->actingAs($analyst)->patch(route('validasi.reject', $knowledge->id), [
        'alasan_tolak' => 'Konten belum melampirkan dasar regulasi yang valid.',
    ]);

    $responseReject->assertRedirect(route('validasi.index'));
    $responseReject->assertSessionHas('success');

    $knowledge->refresh();
    expect($knowledge->status)->toBe('Ditolak');
});

it('prevents regular member from accessing validation routes', function () {
    $member = User::factory()->create(['role' => 'Anggota']);

    $response = $this->actingAs($member)->get(route('validasi.index'));
    $response->assertStatus(403);
});
