<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\Tag;
use App\Models\User;
use App\Services\KnowledgeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

it('creates knowledge and syncs tags atomically within database transaction', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Keamanan Siber']);
    $file = UploadedFile::fake()->create('panduan.pdf', 100);

    $service = app(KnowledgeService::class);
    $knowledge = $service->createKnowledge(
        data: [
            'category_id' => $category->id,
            'judul' => 'Pedoman Keamanan Siber Atomik',
            'deskripsi' => 'Deskripsi materi atomik',
            'tipe' => 'Teks',
            'tags' => ['siber', 'keamanan', 'pedoman'],
        ],
        user: $user,
        fileUpload: $file
    );

    expect($knowledge->id)->not->toBeNull();
    expect($knowledge->tags)->toHaveCount(3);
    expect(Tag::whereIn('nama_label', ['siber', 'keamanan', 'pedoman'])->count())->toBe(3);
    Storage::disk('public')->assertExists($knowledge->file_path);
});

it('rolls back database and uploaded files when knowledge transaction fails', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'Anggota']);
    $file = UploadedFile::fake()->create('gagal.pdf', 100);

    $service = app(KnowledgeService::class);

    $initialKnowledgeCount = Knowledge::count();

    expect(function () use ($service, $file, $user) {
        $service->createKnowledge(
            data: [
                'category_id' => 999999, // ID kategori tidak ada -> memicu Foreign Key Exception di DB
                'judul' => 'Materi Gagal Simpan',
                'tipe' => 'Teks',
                'tags' => ['tag1'],
            ],
            user: $user,
            fileUpload: $file
        );
    })->toThrow(\Illuminate\Database\QueryException::class);

    // Pastikan tidak ada data yang tersimpan (Rollback)
    expect(Knowledge::count())->toBe($initialKnowledgeCount);
    // Pastikan file yang diunggah tidak tertinggal di disk storage
    expect(Storage::disk('public')->allFiles('uploads'))->toBeEmpty();
});

it('atomically updates knowledge and tags during validation process', function () {
    Storage::fake('public');

    $analyst = User::factory()->create(['role' => 'Analisis Pengetahuan']);
    $category1 = Category::create(['nama_kategori' => 'Kategori Lama']);
    $category2 = Category::create(['nama_kategori' => 'Kategori Baru']);

    $knowledge = Knowledge::create([
        'user_id' => $analyst->id,
        'category_id' => $category1->id,
        'judul' => 'Judul Sebelum Validasi',
        'tipe' => 'Teks',
        'status' => 'Diajukan',
    ]);

    $file = UploadedFile::fake()->create('materi_baru.pdf', 100);

    $response = $this->actingAs($analyst)->put(route('validasi.update', $knowledge->id), [
        'judul' => 'Judul Sesudah Validasi',
        'tipe' => 'Teks',
        'category_id' => $category2->id,
        'tags' => 'valid,teruji,resmi',
        'file' => $file,
    ]);

    $response->assertSessionHas('success');
    $knowledge->refresh();

    expect($knowledge->judul)->toBe('Judul Sesudah Validasi');
    expect($knowledge->category_id)->toBe($category2->id);
    expect($knowledge->tags)->toHaveCount(3);
    Storage::disk('public')->assertExists($knowledge->file_path);
});

it('atomically stores knowledge and tags via API with sanctum authentication', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'Anggota']);
    Sanctum::actingAs($user);

    $category = Category::create(['nama_kategori' => 'Layanan Publik']);
    $file = UploadedFile::fake()->create('sop.pdf', 100);

    $response = $this->postJson('/api/knowledge', [
        'judul' => 'SOP Layanan Publik API',
        'category_id' => $category->id,
        'tipe' => 'Teks',
        'deskripsi' => 'Deskripsi materi via API',
        'tags' => 'sop,layanan,masyarakat',
        'file' => $file,
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('status', 'success');

    $knowledge = Knowledge::where('judul', 'SOP Layanan Publik API')->first();
    expect($knowledge)->not->toBeNull();
    expect($knowledge->tags)->toHaveCount(3);
    Storage::disk('public')->assertExists($knowledge->file_path);
});
