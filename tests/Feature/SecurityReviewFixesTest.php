<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\User;
use App\Services\KnowledgeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

it('prevents mass assignment of email_verified_at on User model', function () {
    $user = User::create([
        'name' => 'Mass Assign Test',
        'email' => 'massassign@test.com',
        'password' => bcrypt('password123'),
        'email_verified_at' => now(),
    ]);

    expect($user->email_verified_at)->toBeNull();

    // Verify forceFill still works as intended for internal services (e.g., Keycloak)
    $user->forceFill(['email_verified_at' => now()])->save();
    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

it('deletes physical files after database transaction commits in forceDeleteKnowledge', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori File Test']);
    $file = UploadedFile::fake()->create('dokumen.pdf', 50);
    $path = $file->store('knowledge_files', 'public');

    $knowledge = Knowledge::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'Pengetahuan Dengan File',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
        'file_path' => $path,
    ]);

    Storage::disk('public')->assertExists($path);

    $service = app(KnowledgeService::class);
    $result = $service->forceDeleteKnowledge($knowledge);

    expect($result)->toBeTrue();
    expect(Knowledge::find($knowledge->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('forbids unauthorized access to unapproved knowledge in KnowledgeApiController show', function () {
    $author = User::factory()->create(['role' => 'Anggota']);
    $otherUser = User::factory()->create(['role' => 'Anggota']);
    $analyst = User::factory()->create(['role' => 'Analis Pengetahuan']);
    $admin = User::factory()->create(['role' => 'Admin']);
    $category = Category::create(['nama_kategori' => 'Kategori API Test']);

    $draft = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'API Draft Materi',
        'tipe' => 'Teks',
        'status' => 'Draft',
        'views_count' => 0,
    ]);

    // Guest request -> 403
    $this->getJson("/api/knowledge/{$draft->id}")
        ->assertStatus(403)
        ->assertJsonPath('status', 'error');

    // Other member request -> 403
    Sanctum::actingAs($otherUser);
    $this->getJson("/api/knowledge/{$draft->id}")
        ->assertStatus(403);

    // Author request -> 200 (allowed) & views_count is not incremented for drafts
    Sanctum::actingAs($author);
    $this->getJson("/api/knowledge/{$draft->id}")
        ->assertStatus(200)
        ->assertJsonPath('status', 'success');
    expect($draft->fresh()->views_count)->toBe(0);

    // Analyst request -> 200 (allowed)
    Sanctum::actingAs($analyst);
    $this->getJson("/api/knowledge/{$draft->id}")
        ->assertStatus(200);

    // Admin request -> 200 (allowed)
    Sanctum::actingAs($admin);
    $this->getJson("/api/knowledge/{$draft->id}")
        ->assertStatus(200);
});

it('increments views_count for approved knowledge in KnowledgeApiController show', function () {
    $user = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori API Test']);

    $approved = Knowledge::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'API Approved Materi',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
        'views_count' => 5,
    ]);

    $this->getJson("/api/knowledge/{$approved->id}")
        ->assertStatus(200)
        ->assertJsonPath('status', 'success');

    expect($approved->fresh()->views_count)->toBe(6);
});

it('does not expose sensitive user data in SearchController apiSearch', function () {
    $user = User::factory()->create([
        'name' => 'Budi Santoso',
        'email' => 'budi.private@test.com',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Rahasia No. 123',
        'instansi' => 'Dinas Rahasia',
        'keycloak_id' => 'kc-uuid-999999',
        'role' => 'Anggota',
    ]);
    $category = Category::create(['nama_kategori' => 'Kategori Search Test']);

    Knowledge::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'Panduan Keamanan Siber Publik',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    $response = $this->getJson('/api/search?q=Keamanan');
    $response->assertStatus(200);

    $data = $response->json();
    expect($data)->toBeArray();
    expect(count($data))->toBeGreaterThanOrEqual(1);

    $firstItem = $data[0];
    expect($firstItem['judul'])->toBe('Panduan Keamanan Siber Publik');
    expect($firstItem)->toHaveKey('user');
    expect($firstItem['user'])->toHaveKey('name');
    expect($firstItem['user']['name'])->toBe('Budi Santoso');

    // Verify sensitive fields are completely omitted from user payload
    expect($firstItem['user'])->not->toHaveKey('keycloak_id');
    expect($firstItem['user'])->not->toHaveKey('no_telepon');
    expect($firstItem['user'])->not->toHaveKey('alamat');
    expect($firstItem['user'])->not->toHaveKey('instansi');
    expect($firstItem['user'])->not->toHaveKey('email');
});
