<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('reading_time accessor calculates reading time correctly based on 200 words per minute', function () {
    $knowledge = new Knowledge([
        'deskripsi' => 'Deskripsi singkat artikel.',
        'detail'    => str_repeat('kata ', 395), // ~398 kata total => ceil(398 / 200) = 2 menit
    ]);

    expect($knowledge->reading_time)->toBe(2);

    $shortKnowledge = new Knowledge([
        'deskripsi' => 'Pendek saja.',
        'detail'    => 'Sangat pendek.',
    ]);

    // Minimum reading time is 1 minute
    expect($shortKnowledge->reading_time)->toBe(1);
});

test('canBeViewedBy allows public access when knowledge is Disetujui', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori Akses']);

    $approved = Knowledge::create([
        'user_id'     => $author->id,
        'category_id' => $category->id,
        'judul'       => 'Materi Disetujui',
        'tipe'        => 'Teks',
        'status'      => 'Disetujui',
    ]);

    expect($approved->canBeViewedBy(null))->toBeTrue();

    $otherUser = User::factory()->create(['role' => 'Anggota']);
    expect($approved->canBeViewedBy($otherUser))->toBeTrue();
});

test('canBeViewedBy restricts non-disetujui knowledge to author, admin, and analyst', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $otherMember = User::factory()->create(['role' => 'Anggota']);
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $analyst = User::factory()->create(['role' => 'Analisis Pengetahuan']);
    $category = Category::create(['nama_kategori' => 'Kategori Unapproved']);

    $draft = Knowledge::create([
        'user_id'     => $author->id,
        'category_id' => $category->id,
        'judul'       => 'Materi Draf Rahasia',
        'tipe'        => 'Teks',
        'status'      => 'Draft',
    ]);

    // Guest cannot view
    expect($draft->canBeViewedBy(null))->toBeFalse();

    // Other member cannot view
    expect($draft->canBeViewedBy($otherMember))->toBeFalse();

    // Author, Admin, Analyst CAN view
    expect($draft->canBeViewedBy($author))->toBeTrue();
    expect($draft->canBeViewedBy($admin))->toBeTrue();
    expect($draft->canBeViewedBy($analyst))->toBeTrue();
});

test('recordView increments views_count with debounce cache', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori View Debounce']);

    $knowledge = Knowledge::create([
        'user_id'     => $author->id,
        'category_id' => $category->id,
        'judul'       => 'Artikel Uji View Count',
        'tipe'        => 'Teks',
        'status'      => 'Disetujui',
        'views_count' => 0,
    ]);

    $visitor1 = User::factory()->create(['role' => 'Anggota']);
    $visitor2 = User::factory()->create(['role' => 'Anggota']);

    // First visit by visitor 1 increments
    $firstResult = $knowledge->recordView($visitor1);
    expect($firstResult)->toBeTrue();
    expect($knowledge->fresh()->views_count)->toBe(1);

    // Repeated visit by visitor 1 within debounce does NOT increment
    $secondResult = $knowledge->recordView($visitor1);
    expect($secondResult)->toBeFalse();
    expect($knowledge->fresh()->views_count)->toBe(1);

    // Visit by visitor 2 increments
    $thirdResult = $knowledge->recordView($visitor2);
    expect($thirdResult)->toBeTrue();
    expect($knowledge->fresh()->views_count)->toBe(2);
});

test('scopeFilter filters by status and text search properly', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Teknologi Informasi']);

    Knowledge::create([
        'user_id'     => $author->id,
        'category_id' => $category->id,
        'judul'       => 'Panduan Laravel Pemula',
        'deskripsi'   => 'Tutorial dasar',
        'tipe'        => 'Teks',
        'status'      => 'Disetujui',
    ]);

    Knowledge::create([
        'user_id'     => $author->id,
        'category_id' => $category->id,
        'judul'       => 'Panduan Python Pemula',
        'deskripsi'   => 'Tutorial dasar Python',
        'tipe'        => 'Teks',
        'status'      => 'Diajukan',
    ]);

    $filteredStatus = Knowledge::filter(['status' => 'diajukan'])->get();
    expect($filteredStatus)->toHaveCount(1);
    expect($filteredStatus->first()->judul)->toBe('Panduan Python Pemula');

    $filteredSearch = Knowledge::filter(['q' => 'Laravel'])->get();
    expect($filteredSearch)->toHaveCount(1);
    expect($filteredSearch->first()->judul)->toBe('Panduan Laravel Pemula');
});
