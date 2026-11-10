<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('knowledge');
    $this->owner = User::factory()->create(['role' => 'Anggota']);
    $this->category = Category::create(['nama_kategori' => 'Protected files']);
});

it('authorizes every supported knowledge file field', function ($field, $path) {
    Storage::disk('knowledge')->put($path, 'private contents');
    $knowledge = Knowledge::create([
        'user_id' => $this->owner->id,
        'category_id' => $this->category->id,
        'judul' => 'Private knowledge', 'tipe' => 'Teks',
        'status' => 'Disetujui', 'status_akses' => 'private',
        $field => $path,
    ]);

    $this->get('/storage/'.$path)->assertNotFound();
    auth()->forgetGuards();
    $this->actingAs(User::factory()->create(['role' => 'Anggota']))
        ->get('/storage/'.$path)->assertNotFound();
    auth()->forgetGuards();
    $response = $this->actingAs($this->owner)->get('/storage/'.$path)->assertOk();
    expect($response->headers->get('Cache-Control'))->toContain('no-store');
    auth()->forgetGuards();
    $this->actingAs(User::factory()->create(['role' => 'Admin']))
        ->get('/storage/'.$path)->assertOk();
    auth()->forgetGuards();
    $this->actingAs(User::factory()->create(['role' => 'Analisis Pengetahuan']))
        ->get('/storage/'.$path)->assertOk();

    $knowledge->delete();
    $this->get('/storage/'.$path)->assertNotFound();
})->with([
    ['file_path', 'knowledge_files/private.pdf'],
    ['document_path', 'documents/private.pdf'],
    ['gambar_sampul', 'knowledge_covers/private.png'],
    ['url_teks', 'uploads/private.mp3'],
]);

it('immediately enforces changes from public to private or draft without changing the URL', function () {
    $path = 'uploads/article.txt';
    Storage::disk('knowledge')->put($path, 'article');
    $knowledge = Knowledge::create([
        'user_id' => $this->owner->id, 'category_id' => $this->category->id,
        'judul' => 'Public article', 'tipe' => 'Teks',
        'status' => 'Disetujui', 'status_akses' => 'public', 'file_path' => $path,
    ]);
    $this->get('/storage/'.$path)->assertOk();
    $knowledge->update(['status_akses' => 'private']);
    $this->get('/storage/'.$path)->assertNotFound();
    $knowledge->update(['status_akses' => 'public', 'status' => 'Draft']);
    $this->get('/storage/'.$path)->assertNotFound();
});

it('stores API uploads outside public storage and accepts the owners bearer token for download', function () {
    $token = $this->owner->createToken('test')->plainTextToken;
    $response = $this->withToken($token)->postJson('/api/knowledge', [
        'judul' => 'Private upload', 'tipe' => 'Teks',
        'category_id' => $this->category->id, 'status_akses' => 'private',
        'file' => UploadedFile::fake()->create('private.pdf', 10),
        'gambar_sampul' => UploadedFile::fake()->image('cover.png'),
    ])->assertCreated();

    $knowledge = Knowledge::findOrFail($response->json('data.id'));
    foreach ([$knowledge->file_path, $knowledge->gambar_sampul] as $path) {
        Storage::disk('public')->assertMissing($path);
        Storage::disk('knowledge')->assertExists($path);
        auth()->forgetGuards();
        $this->withToken($token)->get('/storage/'.$path)->assertOk();
    }
});

it('does not serve unreferenced or missing files', function () {
    Storage::disk('knowledge')->put('uploads/orphan.txt', 'secret');
    $this->actingAs($this->owner)->get('/storage/uploads/orphan.txt')->assertNotFound();
    $this->get('/storage/documents/missing.pdf')->assertNotFound();
});

it('moves legacy public copies safely and can be run again', function () {
    foreach (['uploads', 'documents', 'knowledge_files', 'knowledge_covers'] as $directory) {
        Storage::disk('public')->put($directory.'/legacy.txt', 'legacy contents');
    }
    $this->artisan('knowledge:protect-files')->assertSuccessful();
    foreach (['uploads', 'documents', 'knowledge_files', 'knowledge_covers'] as $directory) {
        Storage::disk('public')->assertMissing($directory.'/legacy.txt');
        expect(Storage::disk('knowledge')->get($directory.'/legacy.txt'))->toBe('legacy contents');
    }
    $this->artisan('knowledge:protect-files')->assertSuccessful();
});

it('retains both originals when the migration detects conflicting files', function () {
    Storage::disk('public')->put('uploads/conflict.txt', 'old');
    Storage::disk('knowledge')->put('uploads/conflict.txt', 'different');
    expect(fn () => $this->artisan('knowledge:protect-files')->run())
        ->toThrow(RuntimeException::class);
    expect(Storage::disk('public')->get('uploads/conflict.txt'))->toBe('old')
        ->and(Storage::disk('knowledge')->get('uploads/conflict.txt'))->toBe('different');
});
