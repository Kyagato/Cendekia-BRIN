<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\User;
use App\Services\KnowledgeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
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

it('hides email and email_verified_at in UserResource for public visitors but shows for admin or self', function () {
    $author = User::factory()->create([
        'name' => 'Penulis Publik',
        'email' => 'penulis.rahasia@test.com',
        'email_verified_at' => now(),
    ]);
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $category = Category::create(['nama_kategori' => 'Kategori Privasi']);

    $approved = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Privasi Email',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    // 1. Guest request -> email and email_verified_at MUST NOT be present
    $responseGuest = $this->getJson("/api/knowledge/{$approved->id}");
    $responseGuest->assertStatus(200);
    $guestUserData = $responseGuest->json('data.user');
    expect($guestUserData)->not->toHaveKey('email');
    expect($guestUserData)->not->toHaveKey('email_verified_at');
    expect($guestUserData['name'])->toBe('Penulis Publik');

    // 2. Author request (self) -> email and email_verified_at ARE visible
    Sanctum::actingAs($author);
    $responseAuthor = $this->getJson("/api/knowledge/{$approved->id}");
    $responseAuthor->assertStatus(200);
    $authorUserData = $responseAuthor->json('data.user');
    expect($authorUserData)->toHaveKey('email');
    expect($authorUserData['email'])->toBe('penulis.rahasia@test.com');
    expect($authorUserData)->toHaveKey('email_verified_at');

    // 3. Admin request -> email and email_verified_at ARE visible
    Sanctum::actingAs($admin);
    $responseAdmin = $this->getJson("/api/knowledge/{$approved->id}");
    $responseAdmin->assertStatus(200);
    $adminUserData = $responseAdmin->json('data.user');
    expect($adminUserData)->toHaveKey('email');
    expect($adminUserData['email'])->toBe('penulis.rahasia@test.com');
});

it('invalidates stats cache when knowledge is approved, rejected, or submission is cancelled', function () {
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $author = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori Validasi Cache']);

    $knowledge = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Materi Uji Cache Validasi',
        'tipe' => 'Teks',
        'status' => 'Diajukan',
    ]);

    // 1. Approve invalidates stats cache
    Cache::put('stats_global_aggregation', ['cached_data'], 300);
    Cache::put('stats_instansi_list', ['cached_instansi'], 900);
    expect(Cache::has('stats_global_aggregation'))->toBeTrue();

    $this->actingAs($admin)->patch(route('validasi.approve', $knowledge->id));
    expect(Cache::has('stats_global_aggregation'))->toBeFalse();
    expect(Cache::has('stats_instansi_list'))->toBeFalse();

    // 2. Reject invalidates stats cache
    $knowledge->update(['status' => 'Diajukan']);
    Cache::put('stats_global_aggregation', ['cached_data'], 300);

    $this->actingAs($admin)->patch(route('validasi.reject', $knowledge->id), [
        'alasan_tolak' => 'Uji penolakan cache',
    ]);
    expect(Cache::has('stats_global_aggregation'))->toBeFalse();

    // 3. Cancel submission (batal_ajukan) invalidates stats cache
    $knowledge->update(['status' => 'Diajukan']);
    Cache::put('stats_global_aggregation', ['cached_data'], 300);
    Cache::put('homepage_data', ['cached_homepage'], 600);

    $this->actingAs($author)->put(route('knowledge.update', $knowledge->id), [
        'batal_ajukan' => '1',
    ]);
    expect(Cache::has('stats_global_aggregation'))->toBeFalse();
    expect(Cache::has('homepage_data'))->toBeFalse();
});

it('caches homepage_data on HomeController@index and invalidates it when knowledge is mutated', function () {
    Cache::forget('homepage_data');

    $user = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori Beranda Cache']);

    Knowledge::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'Materi Beranda Cache',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    expect(Cache::has('homepage_data'))->toBeFalse();

    $response = $this->get('/');
    $response->assertStatus(200);
    expect(Cache::has('homepage_data'))->toBeTrue();

    // Mutating knowledge via KnowledgeService invalidates homepage_data
    $service = app(KnowledgeService::class);
    $service->createKnowledge([
        'judul' => 'Materi Baru Penghapus Cache',
        'category_id' => $category->id,
        'tipe' => 'Teks',
        'deskripsi' => 'Deskripsi baru',
        'status' => 'Draft',
    ], $user);

    expect(Cache::has('homepage_data'))->toBeFalse();
});

it('debounces views_count increments and does not record KNOWLEDGE_VIEW audit logs in HomeController@knowledgeShow', function () {
    $user = User::factory()->create(['role' => 'Anggota']);
    $otherUser = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori Debounce View']);

    $knowledge = Knowledge::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'Materi Uji Debounce View',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
        'views_count' => 10,
    ]);

    // First visit by $user increments views_count to 11
    $this->actingAs($user)
        ->get("/knowledge/{$knowledge->id}")
        ->assertStatus(200);

    expect($knowledge->fresh()->views_count)->toBe(11);

    // Repeated visits by the same $user within 2 hours do NOT increment views_count
    $this->actingAs($user)
        ->get("/knowledge/{$knowledge->id}")
        ->assertStatus(200);
    $this->actingAs($user)
        ->get("/knowledge/{$knowledge->id}")
        ->assertStatus(200);

    expect($knowledge->fresh()->views_count)->toBe(11);

    // Visit by a different user ($otherUser) increments views_count to 12
    $this->actingAs($otherUser)
        ->get("/knowledge/{$knowledge->id}")
        ->assertStatus(200);

    expect($knowledge->fresh()->views_count)->toBe(12);

    // Ensure no KNOWLEDGE_VIEW audit log entries were created
    $viewLogsCount = \App\Models\AuditLog::where('action', 'KNOWLEDGE_VIEW')->count();
    expect($viewLogsCount)->toBe(0);
});

