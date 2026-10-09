<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\User;

it('forbids other members from accessing unapproved knowledge on dashboard detail', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $otherMember = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori Akses']);

    $draft = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Draf Penulis',
        'tipe' => 'Teks',
        'status' => 'Draft',
    ]);

    $pending = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Diajukan Penulis',
        'tipe' => 'Teks',
        'status' => 'Diajukan',
    ]);

    $rejected = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Ditolak Penulis',
        'tipe' => 'Teks',
        'status' => 'Ditolak',
        'catatan_penolakan' => 'Data belum lengkap.',
    ]);

    // User lain (bukan author, bukan admin, bukan analis) dilarang melihat materi non-Disetujui
    $this->actingAs($otherMember)->get(route('admin.knowledge.show', $draft->id))
        ->assertStatus(403);

    $this->actingAs($otherMember)->get(route('admin.knowledge.show', $pending->id))
        ->assertStatus(403);

    $this->actingAs($otherMember)->get(route('admin.knowledge.show', $rejected->id))
        ->assertStatus(403);
});

it('allows author to view their own draft, pending, and rejected knowledge on dashboard detail', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori Akses']);

    $draft = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Draf Milik Saya',
        'tipe' => 'Teks',
        'status' => 'Draft',
    ]);

    $pending = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Diajukan Milik Saya',
        'tipe' => 'Teks',
        'status' => 'Diajukan',
    ]);

    $rejected = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Ditolak Milik Saya',
        'tipe' => 'Teks',
        'status' => 'Ditolak',
        'catatan_penolakan' => 'Perbaiki lampiran PDF.',
    ]);

    // Penulis sendiri dapat melihat semua status artikel miliknya
    $this->actingAs($author)->get(route('admin.knowledge.show', $draft->id))
        ->assertStatus(200)
        ->assertSee('Materi Draf Milik Saya');

    $this->actingAs($author)->get(route('admin.knowledge.show', $pending->id))
        ->assertStatus(200)
        ->assertSee('Materi Diajukan Milik Saya');

    $this->actingAs($author)->get(route('admin.knowledge.show', $rejected->id))
        ->assertStatus(200)
        ->assertSee('Materi Ditolak Milik Saya')
        ->assertSee('Perbaiki lampiran PDF.')
        ->assertSee('Catatan Penolakan dari Validator');
});

it('allows analyst and admin to view pending and rejected knowledge on dashboard detail', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $analyst = User::factory()->create(['role' => 'Analisis Pengetahuan']);
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $category = Category::create(['nama_kategori' => 'Kategori Akses']);

    $pending = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Menunggu Validasi',
        'tipe' => 'Teks',
        'status' => 'Diajukan',
    ]);

    // Analis dan Admin dapat mengakses materi yang diajukan
    $this->actingAs($analyst)->get(route('admin.knowledge.show', $pending->id))
        ->assertStatus(200)
        ->assertSee('Materi Menunggu Validasi');

    $this->actingAs($admin)->get(route('admin.knowledge.show', $pending->id))
        ->assertStatus(200)
        ->assertSee('Materi Menunggu Validasi');
});

it('allows any member to view approved knowledge but hides edit and delete actions for non-author', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $otherMember = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori Akses']);

    $approved = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Terbit Publik',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    // User lain dapat melihat materi yang sudah Disetujui
    $responseOther = $this->actingAs($otherMember)->get(route('admin.knowledge.show', $approved->id));
    $responseOther->assertStatus(200)
        ->assertSee('Materi Terbit Publik');

    // Tapi tombol Edit dan Pindahkan ke Tong Sampah disembunyikan
    $responseOther->assertDontSee(route('knowledge.edit', $approved->id));
    $responseOther->assertDontSee('Pindahkan ke Tong Sampah');

    // Penulis asli dapat melihat tombol Edit dan tombol Pindahkan ke Tong Sampah
    $responseAuthor = $this->actingAs($author)->get(route('admin.knowledge.show', $approved->id));
    $responseAuthor->assertStatus(200)
        ->assertSee(route('knowledge.edit', $approved->id))
        ->assertSee('Pindahkan ke Tong Sampah');
});
