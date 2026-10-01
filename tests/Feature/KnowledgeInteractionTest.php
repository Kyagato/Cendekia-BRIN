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
