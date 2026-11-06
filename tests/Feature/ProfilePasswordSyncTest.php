<?php

use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    Cache::flush();
    config(['services.keycloak.base_url' => 'https://sso.example.test',
        'services.keycloak.realms' => 'TestRealm', 'services.keycloak.client_id' => 'test-client',
        'services.keycloak.client_secret' => 'test-secret']);
    Http::preventStrayRequests();
});

it('updates linked profile passwords in Keycloak and locally and revokes API tokens', function () {
    Http::fake([
        '*/protocol/openid-connect/token' => Http::response(['access_token' => 'admin-token']),
        '*/reset-password' => Http::response(null, 204),
    ]);
    $user = User::factory()->create(['keycloak_id' => 'linked-user']);
    $user->createToken('old-token');
    $this->actingAs($user)->patch('/profile', ['name' => $user->name, 'email' => $user->email,
        'password' => 'New-password-123!', 'password_confirmation' => 'New-password-123!'])
        ->assertSessionHasNoErrors()->assertRedirect('/profile');
    expect(Hash::check('New-password-123!', $user->fresh()->password))->toBeTrue();
    expect($user->tokens()->count())->toBe(0);
    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request->url() === 'https://sso.example.test/admin/realms/TestRealm/users/linked-user/reset-password'
        && $request['value'] === 'New-password-123!' && $request['temporary'] === false
        && $request['type'] === 'password' && $request->hasHeader('Authorization', 'Bearer admin-token'));
});

it('keeps local password and tokens when Keycloak rejects the change', function (int $status) {
    Http::fake(['*/protocol/openid-connect/token' => Http::response(['access_token' => 'admin-token']),
        '*/reset-password' => Http::response([], $status)]);
    $user = User::factory()->create(['keycloak_id' => 'linked-user']);
    $oldHash = $user->password;
    $user->createToken('old-token');
    $this->actingAs($user)->from('/profile')->patch('/profile', ['name' => 'Changed name', 'email' => $user->email,
        'password' => 'New-password-123!', 'password_confirmation' => 'New-password-123!'])
        ->assertSessionHasErrors('password')->assertRedirect('/profile');
    expect($user->fresh()->password)->toBe($oldHash);
    expect($user->fresh()->name)->not->toBe('Changed name');
    expect($user->tokens()->count())->toBe(1);
})->with([400, 403, 500]);

it('does not change a linked local password when admin authentication fails', function () {
    Http::fake(['*/protocol/openid-connect/token' => Http::response([], 401)]);
    $user = User::factory()->create(['keycloak_id' => 'linked-user']);
    $oldHash = $user->password;
    $this->actingAs($user)->patch('/profile', ['name' => $user->name, 'email' => $user->email,
        'password' => 'New-password-123!', 'password_confirmation' => 'New-password-123!'])
        ->assertSessionHasErrors('password');
    expect($user->fresh()->password)->toBe($oldHash);
    Http::assertSentCount(1);
});

it('updates a local only account without contacting Keycloak', function () {
    Http::fake();
    $user = User::factory()->create(['keycloak_id' => null]);
    $this->actingAs($user)->patch('/profile', ['name' => $user->name, 'email' => $user->email,
        'password' => 'New-password-123!', 'password_confirmation' => 'New-password-123!'])->assertSessionHasNoErrors();
    expect(Hash::check('New-password-123!', $user->fresh()->password))->toBeTrue();
    Http::assertNothingSent();
});

it('does not contact Keycloak when the new password confirmation is invalid', function () {
    Http::fake();
    $user = User::factory()->create(['keycloak_id' => 'linked-user']);
    $this->actingAs($user)->patch('/profile', ['name' => $user->name, 'email' => $user->email,
        'password' => 'New-password-123!', 'password_confirmation' => 'different'])->assertSessionHasErrors('password');
    Http::assertNothingSent();
});

it('uses the same synchronization on the dedicated password endpoint', function () {
    Http::fake(['*/protocol/openid-connect/token' => Http::response(['access_token' => 'admin-token']),
        '*/reset-password' => Http::response(null, 204)]);
    $user = User::factory()->create(['keycloak_id' => 'linked-user']);
    $this->actingAs($user)->put('/password', ['current_password' => 'password',
        'password' => 'New-password-123!', 'password_confirmation' => 'New-password-123!'])->assertSessionHasNoErrors();
    expect(Hash::check('New-password-123!', $user->fresh()->password))->toBeTrue();
    Http::assertSentCount(2);
});

it('preserves the old profile photo and removes the staged upload on synchronization failure', function () {
    Storage::fake('public');
    Storage::disk('public')->put('profile_photos/old.jpg', 'old');
    Http::fake(['*/protocol/openid-connect/token' => Http::response(['access_token' => 'admin-token']),
        '*/reset-password' => Http::response([], 503)]);
    $user = User::factory()->create(['keycloak_id' => 'linked-user', 'foto_profil' => 'profile_photos/old.jpg']);
    $this->actingAs($user)->patch('/profile', ['name' => $user->name, 'email' => $user->email,
        'password' => 'New-password-123!', 'password_confirmation' => 'New-password-123!',
        'foto_profil' => UploadedFile::fake()->image('new.jpg')])->assertSessionHasErrors('password');
    expect($user->fresh()->foto_profil)->toBe('profile_photos/old.jpg');
    expect(Storage::disk('public')->allFiles())->toBe(['profile_photos/old.jpg']);
});
