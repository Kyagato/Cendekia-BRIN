<?php

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Knowledge;
use App\Models\User;

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $this->adminPusat = User::factory()->create(['role' => User::ROLE_ADMIN_PUSAT]);
    $this->adminRegular = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->analyst = User::factory()->create(['role' => User::ROLE_ANALIS]);
    $this->member = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
    $this->moderator = User::factory()->create(['role' => User::ROLE_MODERATOR]);
});

it('allows exactly 3 admin roles to access edit direktori page', function () {
    // 1. Super Admin
    $this->actingAs($this->superAdmin)->get(route('admin.categories.index'))
        ->assertStatus(200)
        ->assertSee('DIREKTORI KATEGORI');

    // 2. Admin Pusat
    $this->actingAs($this->adminPusat)->get(route('admin.categories.index'))
        ->assertStatus(200)
        ->assertSee('DIREKTORI KATEGORI');

    // 3. Admin
    $this->actingAs($this->adminRegular)->get(route('admin.categories.index'))
        ->assertStatus(200)
        ->assertSee('DIREKTORI KATEGORI');
});

it('forbids analyst, member, moderator, and guest from accessing edit direktori page', function () {
    // Guest redirects to login
    $this->get(route('admin.categories.index'))
        ->assertRedirect('/login');

    // Analisis Pengetahuan gets 403 Forbidden
    $this->actingAs($this->analyst)->get(route('admin.categories.index'))
        ->assertStatus(403);

    // Member gets 403 Forbidden
    $this->actingAs($this->member)->get(route('admin.categories.index'))
        ->assertStatus(403);

    // Moderator gets 403 Forbidden
    $this->actingAs($this->moderator)->get(route('admin.categories.index'))
        ->assertStatus(403);
});

it('allows admin to store a new category and records audit log', function () {
    $response = $this->actingAs($this->adminRegular)->post(route('admin.categories.store'), [
        'nama_kategori' => 'Kecerdasan Buatan & AI',
    ]);

    $response->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success');

    $category = Category::where('nama_kategori', 'Kecerdasan Buatan & AI')->first();
    expect($category)->not->toBeNull();

    $log = AuditLog::where('action', 'CATEGORY_CREATED')
        ->where('user_id', $this->adminRegular->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->properties['nama_kategori'])->toBe('Kecerdasan Buatan & AI');
});

it('forbids analyst from storing a new category', function () {
    $response = $this->actingAs($this->analyst)->post(route('admin.categories.store'), [
        'nama_kategori' => 'Kategori Dititip Analis',
    ]);

    $response->assertStatus(403);
    expect(Category::where('nama_kategori', 'Kategori Dititip Analis')->exists())->toBeFalse();
});

it('prevents creating duplicate category name', function () {
    Category::create(['nama_kategori' => 'Kategori Duplikat']);

    $response = $this->actingAs($this->superAdmin)->post(route('admin.categories.store'), [
        'nama_kategori' => 'Kategori Duplikat',
    ]);

    $response->assertSessionHasErrors(['nama_kategori']);
});

it('allows updating an existing category name and records audit log', function () {
    $category = Category::create(['nama_kategori' => 'Kategori Lama']);

    $response = $this->actingAs($this->adminPusat)->put(route('admin.categories.update', $category->id), [
        'nama_kategori' => 'Kategori Baru Diperbarui',
    ]);

    $response->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success');

    $category->refresh();
    expect($category->nama_kategori)->toBe('Kategori Baru Diperbarui');

    $log = AuditLog::where('action', 'CATEGORY_UPDATED')
        ->where('user_id', $this->adminPusat->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->properties['old_name'])->toBe('Kategori Lama');
    expect($log->properties['new_name'])->toBe('Kategori Baru Diperbarui');
});

it('prevents deleting a category if it still has associated knowledge articles', function () {
    $category = Category::create(['nama_kategori' => 'Kategori Berisi']);
    Knowledge::create([
        'user_id'     => $this->superAdmin->id,
        'category_id' => $category->id,
        'judul'       => 'Artikel Pengujian Kategori',
        'tipe'        => 'Teks',
        'status'      => 'Disetujui',
    ]);

    $response = $this->actingAs($this->superAdmin)->delete(route('admin.categories.destroy', $category->id));

    $response->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('error');

    expect(Category::where('id', $category->id)->exists())->toBeTrue();
});

it('prevents deleting a category if its knowledge articles are in trash (soft-deleted)', function () {
    $category = Category::create(['nama_kategori' => 'Kategori Dengan Trash']);
    $knowledge = Knowledge::create([
        'user_id'     => $this->superAdmin->id,
        'category_id' => $category->id,
        'judul'       => 'Artikel Masuk Tong Sampah',
        'tipe'        => 'Teks',
        'status'      => 'Disetujui',
    ]);

    // Soft delete the knowledge
    $knowledge->delete();
    expect($knowledge->trashed())->toBeTrue();

    // Web request -> deletion must be rejected
    $response = $this->actingAs($this->superAdmin)->delete(route('admin.categories.destroy', $category->id));
    $response->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('error');

    expect(Category::where('id', $category->id)->exists())->toBeTrue();

    // API request -> deletion must also be rejected with 400
    Laravel\Sanctum\Sanctum::actingAs($this->superAdmin);
    $apiResponse = $this->deleteJson("/api/admin/categories/{$category->id}");
    $apiResponse->assertStatus(400)
        ->assertJsonPath('status', 'error');

    expect(Category::where('id', $category->id)->exists())->toBeTrue();
});

it('allows deleting an empty category and records audit log', function () {
    $category = Category::create(['nama_kategori' => 'Kategori Kosong']);

    $response = $this->actingAs($this->adminRegular)->delete(route('admin.categories.destroy', $category->id));

    $response->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success');

    expect(Category::where('id', $category->id)->exists())->toBeFalse();

    $log = AuditLog::where('action', 'CATEGORY_DELETED')
        ->where('user_id', $this->adminRegular->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->properties['nama_kategori'])->toBe('Kategori Kosong');
});

it('shows Edit Kategori button on knowledge index exclusively for 3 admin roles', function () {
    // 3 Admin roles can see the button
    $this->actingAs($this->superAdmin)->get(route('knowledge.index'))
        ->assertStatus(200)
        ->assertSee('Edit Kategori')
        ->assertSee(route('admin.categories.index'));

    $this->actingAs($this->adminPusat)->get(route('knowledge.index'))
        ->assertStatus(200)
        ->assertSee('Edit Kategori')
        ->assertSee(route('admin.categories.index'));

    $this->actingAs($this->adminRegular)->get(route('knowledge.index'))
        ->assertStatus(200)
        ->assertSee('Edit Kategori')
        ->assertSee(route('admin.categories.index'));

    // Other roles CANNOT see the button
    $this->actingAs($this->analyst)->get(route('knowledge.index'))
        ->assertStatus(200)
        ->assertDontSee('Edit Kategori');

    $this->actingAs($this->member)->get(route('knowledge.index'))
        ->assertStatus(200)
        ->assertDontSee('Edit Kategori');

    $this->actingAs($this->moderator)->get(route('knowledge.index'))
        ->assertStatus(200)
        ->assertDontSee('Edit Kategori');
});

