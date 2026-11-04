<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('replaces only the authenticated users banner and removes the previous image', function () {
    Storage::fake('public');
    $owner = User::factory()->create();
    $other = User::factory()->create();
    Storage::disk('public')->put('profile_banners/old.jpg', 'old');
    $owner->forceFill(['banner_path' => 'profile_banners/old.jpg'])->save();

    $this->actingAs($owner)->post('/profile/banner', [
        'banner' => UploadedFile::fake()->image('cover.jpg'),
        'user_id' => $other->id,
    ])->assertSessionHasNoErrors()->assertRedirect(route('users.show', $owner));

    Storage::disk('public')->assertExists($owner->fresh()->banner_path);
    Storage::disk('public')->assertMissing('profile_banners/old.jpg');
    expect($other->fresh()->banner_path)->toBeNull();
    $this->get(route('users.show', $owner))->assertInertia(fn ($page) => $page
        ->component('Profile/Show')->where('user.banner_path', $owner->fresh()->banner_path));
});

it('rejects non-image and oversized banner uploads', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    foreach ([UploadedFile::fake()->create('bad.svg', 1, 'image/svg+xml'), UploadedFile::fake()->image('large.jpg')->size(5121)] as $file) {
        $this->actingAs($user)->post('/profile/banner', ['banner' => $file])->assertSessionHasErrors('banner');
    }
    expect($user->fresh()->banner_path)->toBeNull();
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

it('requires authentication to change the banner', function () {
    $this->post('/profile/banner')->assertRedirect('/login');
});
