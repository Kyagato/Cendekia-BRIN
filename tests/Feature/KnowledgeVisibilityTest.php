<?php

use App\Models\Category;
use App\Models\ForumThread;
use App\Models\Knowledge;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

function restrictedReviewMaterial(User $owner, array $attributes = []): Knowledge
{
    return Knowledge::create(array_merge([
        'user_id' => $owner->id,
        'category_id' => Category::create(['nama_kategori' => 'Visibility'])->id,
        'judul' => 'Restricted', 'deskripsi' => 'CONFIDENTIAL', 'tipe' => 'Teks',
        'status' => 'Disetujui', 'status_akses' => 'private', 'views_count' => 10,
    ], $attributes));
}

it('excludes private material from public profile payload and totals', function () {
    $owner = User::factory()->create();
    $private = restrictedReviewMaterial($owner);
    $public = restrictedReviewMaterial($owner, ['status_akses' => 'public', 'views_count' => 2]);
    $this->get(route('users.show', $owner))->assertInertia(fn ($page) => $page
        ->has('knowledgeList.data', 1)->where('knowledgeList.data.0.id', $public->id)
        ->where('totalKnowledge', 1)->where('totalViews', 2));
});

it('denies bookmarking inaccessible private or draft material', function (string $status) {
    $owner = User::factory()->create();
    $viewer = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
    $material = restrictedReviewMaterial($owner, ['status' => $status]);
    $this->actingAs($viewer)->postJson('/knowledge/'.$material->id.'/bookmark')->assertNotFound();
    $this->assertDatabaseMissing('bookmarks', ['knowledge_id' => $material->id, 'user_id' => $viewer->id]);
})->with(['Disetujui', 'Draft', 'Diajukan', 'Ditolak']);

it('hides old bookmarks after material becomes private in JSON and both profile lists', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
    $material = restrictedReviewMaterial($owner, ['status_akses' => 'public']);
    $this->actingAs($viewer)->postJson('/knowledge/'.$material->id.'/bookmark')->assertOk();
    $material->update(['status_akses' => 'private']);
    $this->getJson('/bookmarks')->assertJsonCount(0, 'data');
    $this->get('/bookmarks')->assertInertia(fn ($page) => $page->has('knowledges.data', 0));
    $this->get(route('users.show', $viewer))->assertInertia(fn ($page) => $page
        ->has('bookmarkedList.data', 0)->where('totalBookmarks', 0));
});

it('allows the owner to bookmark approved private material', function () {
    $owner = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
    $material = restrictedReviewMaterial($owner);
    $this->actingAs($owner)->postJson('/knowledge/'.$material->id.'/bookmark')->assertOk();
    $this->getJson('/bookmarks')->assertJsonPath('data.0.id', $material->id);
});

it('rejects restricted forum selections and links on web and API', function (string $status, string $access) {
    $owner = User::factory()->create();
    $viewer = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
    $material = restrictedReviewMaterial($owner, ['status' => $status, 'status_akses' => $access]);
    $this->actingAs($viewer)->get('/forum/create?knowledge_id='.$material->id)->assertNotFound();
    $this->get('/forum/create')->assertInertia(fn ($page) => $page->has('knowledges', 0));
    $payload = ['judul' => 'Thread', 'konten' => 'Discussion', 'category_id' => $material->category_id, 'knowledge_id' => $material->id];
    $this->post('/forum', $payload)->assertSessionHasErrors('knowledge_id');
    $thread = ForumThread::create(['user_id' => $viewer->id, 'category_id' => $material->category_id,
        'judul' => 'Existing', 'konten' => 'Text', 'status' => 'approved']);
    $this->put('/forum/'.$thread->id, $payload)->assertSessionHasErrors('knowledge_id');
    Sanctum::actingAs($viewer);
    $this->postJson('/api/forum/threads', $payload)->assertUnprocessable()->assertJsonValidationErrors('knowledge_id');
})->with([['Draft', 'public'], ['Disetujui', 'private'], ['Ditolak', 'public']]);

it('conceals existing forum links when linked material becomes restricted', function () {
    $owner = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
    $material = restrictedReviewMaterial($owner, ['status_akses' => 'public']);
    $thread = ForumThread::create(['user_id' => $owner->id, 'category_id' => $material->category_id,
        'judul' => 'Existing', 'konten' => 'Text', 'status' => 'approved', 'knowledge_id' => $material->id]);
    expect($thread->knowledge?->id)->toBe($material->id);
    $material->update(['status_akses' => 'private']);
    $this->get('/forum/'.$thread->id)->assertInertia(fn ($page) => $page->where('thread.knowledge', null));
    $this->getJson('/api/forum/threads/'.$thread->id)->assertJsonPath('data.knowledge', null);
    $this->actingAs($owner)->get('/forum/'.$thread->id.'/edit')
        ->assertInertia(fn ($page) => $page->where('thread.knowledge', null)->has('knowledges', 0));
});
