<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\KnowledgeComment;
use App\Models\KnowledgeLike;
use App\Models\User;

it('allows authenticated user to toggle like on a knowledge', function () {
    $user = User::factory()->create();
    $category = Category::create(['nama_kategori' => 'Teknologi Informasi']);
    $knowledge = Knowledge::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'Uji Coba Pengetahuan Like',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    // Like
    $response = $this->actingAs($user)->postJson(route('knowledge.like', $knowledge->id));
    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'liked' => true,
            'total_likes' => 1,
        ]);

    expect(KnowledgeLike::where('knowledge_id', $knowledge->id)->where('user_id', $user->id)->exists())->toBeTrue();

    // Unlike
    $response2 = $this->actingAs($user)->postJson(route('knowledge.like', $knowledge->id));
    $response2->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'liked' => false,
            'total_likes' => 0,
        ]);

    expect(KnowledgeLike::where('knowledge_id', $knowledge->id)->where('user_id', $user->id)->exists())->toBeFalse();
});

it('allows authenticated user to post and delete comment on a knowledge', function () {
    $user = User::factory()->create();
    $category = Category::create(['nama_kategori' => 'Kategori Komentar']);
    $knowledge = Knowledge::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'judul' => 'Uji Coba Pengetahuan Komentar',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    // Post comment
    $response = $this->actingAs($user)->post(route('knowledge.comment', $knowledge->id), [
        'konten' => 'Artikel yang sangat bermanfaat dan komprehensif!',
    ]);

    $response->assertSessionHas('success');
    expect(KnowledgeComment::where('knowledge_id', $knowledge->id)->where('user_id', $user->id)->exists())->toBeTrue();

    $comment = KnowledgeComment::where('knowledge_id', $knowledge->id)->first();

    // Delete comment
    $deleteResponse = $this->actingAs($user)->delete(route('knowledge.comment.destroy', $comment->id));
    $deleteResponse->assertSessionHas('success');
    expect(KnowledgeComment::where('id', $comment->id)->exists())->toBeFalse();
});

it('allows replying with mention_user and handles nested reply structure', function () {
    $author = User::factory()->create();
    $replier1 = User::factory()->create(['name' => 'Budi Pratama']);
    $replier2 = User::factory()->create(['name' => 'Siti Rahma']);
    $category = Category::create(['nama_kategori' => 'Kategori Nested']);
    $knowledge = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Pengetahuan Hierarki Balasan',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    // Root comment (Level 0)
    $rootComment = KnowledgeComment::create([
        'knowledge_id' => $knowledge->id,
        'user_id' => $author->id,
        'konten' => 'Komentar Utama',
    ]);

    // Level 1 Reply
    $responseReply1 = $this->actingAs($replier1)->postJson(route('knowledge.comment', $knowledge->id), [
        'konten' => 'Balasan Level 1',
        'parent_id' => $rootComment->id,
    ]);
    $responseReply1->assertOk()
        ->assertJson([
            'status' => 'success',
        ]);
    $reply1Id = $responseReply1->json('comment.id');

    // Level 2 Sub-reply with mention
    $responseReply2 = $this->actingAs($replier2)->postJson(route('knowledge.comment', $knowledge->id), [
        'konten' => 'Sub-balasan Level 2',
        'parent_id' => $reply1Id,
        'mention_user' => 'Budi Pratama',
    ]);
    $responseReply2->assertOk()
        ->assertJson([
            'status' => 'success',
        ]);

    $html = $responseReply2->json('html');
    expect($html)->toContain('@Budi Pratama')
        ->and($html)->not->toContain('{{ $subReply->mention_user }}');

    $reply2 = KnowledgeComment::find($responseReply2->json('comment.id'));
    expect($reply2->mention_user)->toBe('Budi Pratama')
        ->and($reply2->parent_id)->toBe($reply1Id);
});

it('allows comment author to edit their comment and forbids others', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::create(['nama_kategori' => 'Kategori Edit']);
    $knowledge = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Pengetahuan Edit Komentar',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    $comment = KnowledgeComment::create([
        'knowledge_id' => $knowledge->id,
        'user_id' => $author->id,
        'konten' => 'Teks Asli Komentar',
    ]);

    // Other user cannot edit
    $forbiddenResponse = $this->actingAs($otherUser)->putJson(route('knowledge.comment.update', $comment->id), [
        'konten' => 'Mencoba meretas komentar',
    ]);
    $forbiddenResponse->assertForbidden();
    expect($comment->fresh()->konten)->toBe('Teks Asli Komentar');

    // Author can edit
    $successResponse = $this->actingAs($author)->putJson(route('knowledge.comment.update', $comment->id), [
        'konten' => 'Teks Komentar Telah Diedit',
    ]);
    $successResponse->assertOk()
        ->assertJson([
            'status' => 'success',
            'konten' => 'Teks Komentar Telah Diedit',
        ]);
    expect($comment->fresh()->konten)->toBe('Teks Komentar Telah Diedit');
});

it('allows author, admin, and moderator to delete comment and forbids other users', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create(['role' => 'Pengguna']);
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $moderator = User::factory()->create(['role' => 'Moderator']);
    $category = Category::create(['nama_kategori' => 'Kategori Hapus']);
    $knowledge = Knowledge::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'judul' => 'Pengetahuan Hapus Komentar',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    // Test 1: Other user cannot delete
    $comment1 = KnowledgeComment::create([
        'knowledge_id' => $knowledge->id,
        'user_id' => $author->id,
        'konten' => 'Komentar 1',
    ]);
    $forbiddenResponse = $this->actingAs($otherUser)->deleteJson(route('knowledge.comment.destroy', $comment1->id));
    $forbiddenResponse->assertForbidden();
    expect(KnowledgeComment::where('id', $comment1->id)->exists())->toBeTrue();

    // Test 2: Admin can delete
    $adminResponse = $this->actingAs($admin)->deleteJson(route('knowledge.comment.destroy', $comment1->id));
    $adminResponse->assertOk();
    expect(KnowledgeComment::where('id', $comment1->id)->exists())->toBeFalse();

    // Test 3: Moderator can delete
    $comment2 = KnowledgeComment::create([
        'knowledge_id' => $knowledge->id,
        'user_id' => $author->id,
        'konten' => 'Komentar 2',
    ]);
    $modResponse = $this->actingAs($moderator)->deleteJson(route('knowledge.comment.destroy', $comment2->id));
    $modResponse->assertOk();
    expect(KnowledgeComment::where('id', $comment2->id)->exists())->toBeFalse();

    // Test 4: Author can delete
    $comment3 = KnowledgeComment::create([
        'knowledge_id' => $knowledge->id,
        'user_id' => $author->id,
        'konten' => 'Komentar 3',
    ]);
    $authorResponse = $this->actingAs($author)->deleteJson(route('knowledge.comment.destroy', $comment3->id));
    $authorResponse->assertOk();
    expect(KnowledgeComment::where('id', $comment3->id)->exists())->toBeFalse();
});
