<?php

use App\Models\Category;
use App\Models\ForumThread;
use App\Models\Knowledge;
use App\Models\User;

it('soft deletes knowledge and allows owner to view in trash and restore', function () {
    $user1 = User::factory()->create(['role' => 'Anggota']);
    $user2 = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Kategori Uji']);

    $knowledge1 = Knowledge::create([
        'user_id' => $user1->id,
        'category_id' => $category->id,
        'judul' => 'Pengetahuan User 1',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    $knowledge2 = Knowledge::create([
        'user_id' => $user2->id,
        'category_id' => $category->id,
        'judul' => 'Pengetahuan User 2',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    // User 1 soft deletes her knowledge
    $this->actingAs($user1)->delete(route('knowledge.destroy', $knowledge1->id))
        ->assertRedirect(route('knowledge.index'))
        ->assertSessionHas('success');

    // Pastikan ter-soft delete
    expect(Knowledge::find($knowledge1->id))->toBeNull();
    expect(Knowledge::withTrashed()->find($knowledge1->id))->not->toBeNull();

    // User 2 soft deletes her knowledge
    $this->actingAs($user2)->delete(route('knowledge.destroy', $knowledge2->id));

    // User 1 membuka halaman trash: HANYA melihat artikel miliknya
    $response = $this->actingAs($user1)->get(route('knowledge.trash'));
    $response->assertStatus(200);
    $response->assertSee('Pengetahuan User 1');
    $response->assertDontSee('Pengetahuan User 2');

    // User 1 restores her knowledge
    $this->actingAs($user1)->post(route('knowledge.restore', $knowledge1->id))
        ->assertRedirect(route('knowledge.trash'))
        ->assertSessionHas('success');

    $knowledge1->refresh();
    expect($knowledge1->deleted_at)->toBeNull();

    // User 1 tidak bisa restore milik User 2 (404)
    $this->actingAs($user1)->post(route('knowledge.restore', $knowledge2->id))
        ->assertStatus(404);
});

it('soft deletes forum thread and allows owner to view in dashboard trash and restore', function () {
    $userA = User::factory()->create(['role' => 'Anggota']);
    $userB = User::factory()->create(['role' => 'Anggota']);
    $category = Category::create(['nama_kategori' => 'Diskusi Uji']);

    $threadA = ForumThread::create([
        'user_id' => $userA->id,
        'category_id' => $category->id,
        'judul' => 'Topik Forum Milik A',
        'konten' => 'Isi topik A',
        'status' => 'approved',
    ]);

    $threadB = ForumThread::create([
        'user_id' => $userB->id,
        'category_id' => $category->id,
        'judul' => 'Topik Forum Milik B',
        'konten' => 'Isi topik B',
        'status' => 'approved',
    ]);

    // User A menghapus topiknya sendiri
    $this->actingAs($userA)->delete(route('forum.destroy', $threadA->id), ['from_dashboard' => 1])
        ->assertRedirect(route('dashboard.forum.index'))
        ->assertSessionHas('success');

    expect(ForumThread::find($threadA->id))->toBeNull();
    expect(ForumThread::withTrashed()->find($threadA->id))->not->toBeNull();

    // User B menghapus topiknya
    $this->actingAs($userB)->delete(route('forum.destroy', $threadB->id));

    // User A membuka trash forum: HANYA melihat topik miliknya
    $responseTrash = $this->actingAs($userA)->get(route('dashboard.forum.trash'));
    $responseTrash->assertStatus(200);
    $responseTrash->assertSee('Topik Forum Milik A');
    $responseTrash->assertDontSee('Topik Forum Milik B');

    // User A memulihkan topiknya
    $this->actingAs($userA)->post(route('dashboard.forum.restore', $threadA->id))
        ->assertRedirect(route('dashboard.forum.trash'))
        ->assertSessionHas('success');

    $threadA->refresh();
    expect($threadA->deleted_at)->toBeNull();

    // User A tidak bisa memulihkan topik milik User B (404)
    $this->actingAs($userA)->post(route('dashboard.forum.restore', $threadB->id))
        ->assertStatus(404);
});
