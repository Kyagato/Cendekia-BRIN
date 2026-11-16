<?php

use App\Exceptions\KeycloakDirectoryException;
use App\Models\AuditLog;
use App\Models\KeycloakUserSync;
use App\Models\User;
use App\Services\KeycloakUserCreationService;
use App\Services\KeycloakUserDirectory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->payload = ['request_key' => (string) Str::uuid(), 'username' => 'new.user', 'firstName' => 'New', 'lastName' => 'User',
        'email' => 'new@example.test', 'role' => 'Moderator', 'password' => 'Initial-123!', 'password_confirmation' => 'Initial-123!'];
    $this->actor = User::factory()->create(['role' => 'Super Admin']);
});

it('only allows super admin and admin pusat to create accounts', function ($role, $status) {
    $this->actor->update(['role' => $role]);
    $this->actingAs($this->actor)->get('/admin/users/create')->assertStatus($status);
    if ($status === 403) {
        $this->post('/admin/users', $this->payload)->assertForbidden();
    }
})->with([['Super Admin', 200], ['Admin Pusat', 200], ['Admin', 403], ['Anggota', 403]]);

it('does not offer or accept super admin and admin pusat roles for admin pusat', function () {
    $this->actor->update(['role' => 'Admin Pusat']);
    $this->actingAs($this->actor)->get('/admin/users/create')->assertOk()
        ->assertDontSee('value="Super Admin"', false)->assertDontSee('value="Admin Pusat"', false);
    foreach (['Super Admin', 'Admin Pusat'] as $role) {
        $this->postJson('/admin/users', array_merge($this->payload, ['role' => $role]))->assertUnprocessable();
    }
});

it('creates and verifies Keycloak identity role and local account without logging the password', function () {
    $remote = array_intersect_key($this->payload, array_flip(['username', 'firstName', 'lastName', 'email'])) + ['id' => 'created-id', 'enabled' => true, 'emailVerified' => false];
    $directory = Mockery::mock(KeycloakUserDirectory::class);
    $directory->shouldReceive('create')->once()->withArgs(fn ($payload) => $payload['enabled'] === false
        && $payload['credentials'][0]['value'] === 'Initial-123!')->andReturn('created-id');
    $directory->shouldReceive('setClientRole')->once()->with('created-id', 'Moderator');
    $directory->shouldReceive('update')->once()->withArgs(fn ($id, $payload) => $id === 'created-id' && $payload['enabled'] === true);
    $directory->shouldReceive('user')->with('created-id')->andReturn($remote);
    $directory->shouldReceive('role')->with('created-id')->andReturn('Moderator');
    $directory->shouldReceive('hasClientRole')->with('created-id', 'Moderator')->andReturn(true);
    $this->app->instance(KeycloakUserDirectory::class, $directory);
    $this->actingAs($this->actor)->post('/admin/users', $this->payload)->assertSessionHasNoErrors()->assertRedirect('/admin/users');
    $local = User::where('keycloak_id', 'created-id')->sole();
    expect($local->username)->toBe('new.user')->and($local->role)->toBe('Moderator')
        ->and(Hash::check('Initial-123!', $local->password))->toBeTrue();
    $sync = KeycloakUserSync::sole();
    expect($sync->status)->toBe('synced')->and($sync->local_password_hash)->toBeNull();
    foreach (AuditLog::all() as $audit) {
        expect(json_encode($audit->toArray()))->not->toContain('Initial-123!');
    }
    $service = app(KeycloakUserCreationService::class);
    $service->create($this->actor, $sync->after, 'Initial-123!', $this->payload['request_key']);
    expect(User::where('keycloak_id', 'created-id')->count())->toBe(1);
});

it('rejects bad confirmation and duplicate local email before contacting Keycloak', function () {
    $this->actingAs($this->actor)->postJson('/admin/users', array_merge($this->payload, ['password_confirmation' => 'wrong']))->assertUnprocessable();
    User::factory()->create(['email' => $this->payload['email']]);
    $this->postJson('/admin/users', $this->payload)->assertUnprocessable();
    expect(KeycloakUserSync::count())->toBe(0);
});

it('keeps a recovery record and no local account when remote role assignment fails', function () {
    $directory = Mockery::mock(KeycloakUserDirectory::class);
    $directory->shouldReceive('create')->once()->andReturn('created-id');
    $directory->shouldReceive('setClientRole')->once()->andThrow(new KeycloakDirectoryException(403));
    $this->app->instance(KeycloakUserDirectory::class, $directory);
    $this->actingAs($this->actor)->from('/admin/users/create')->post('/admin/users', $this->payload)
        ->assertSessionHasErrors('keycloak')->assertSessionMissing('success');
    expect(User::where('keycloak_id', 'created-id')->exists())->toBeFalse()
        ->and(KeycloakUserSync::sole()->status)->toBe('reconciliation_required');
    expect(session('_old_input'))->not->toHaveKeys(['password', 'password_confirmation']);
});

it('does not attach an existing account when Keycloak returns a duplicate conflict', function () {
    $directory = Mockery::mock(KeycloakUserDirectory::class);
    $directory->shouldReceive('create')->once()->andThrow(new KeycloakDirectoryException(409));
    $this->app->instance(KeycloakUserDirectory::class, $directory);
    $this->actingAs($this->actor)->post('/admin/users', $this->payload)->assertSessionHasErrors('keycloak');
    expect(User::count())->toBe(1)->and(KeycloakUserSync::sole()->status)->toBe('remote_failed')
        ->and(KeycloakUserSync::sole()->local_password_hash)->toBeNull();
});

it('does not finish local creation when the client role is missing even if the realm role matches', function () {
    $sync = KeycloakUserSync::create(['keycloak_user_id' => 'known-id', 'actor_id' => $this->actor->id,
        'operation' => 'create', 'status' => 'reconciliation_required', 'before' => [],
        'after' => ['username' => 'MixedCase', 'role' => 'Moderator']]);
    $directory = Mockery::mock(KeycloakUserDirectory::class);
    $directory->shouldReceive('user')->with('known-id')->andReturn(['username' => 'mixedcase']);
    $directory->shouldReceive('role')->with('known-id')->andReturn('Moderator');
    $directory->shouldReceive('hasClientRole')->with('known-id', 'Moderator')->andReturn(false);
    $this->app->instance(KeycloakUserDirectory::class, $directory);
    expect(fn () => app(KeycloakUserCreationService::class)->finish($sync))->toThrow(RuntimeException::class);
    expect(User::where('keycloak_id', 'known-id')->exists())->toBeFalse();
});

it('accepts Keycloak lowercase normalization during creation recovery', function () {
    $sync = KeycloakUserSync::create(['keycloak_user_id' => 'known-id', 'actor_id' => $this->actor->id,
        'operation' => 'create', 'status' => 'reconciliation_required', 'before' => [],
        'after' => ['username' => 'MixedCase', 'firstName' => 'New', 'lastName' => 'User', 'email' => 'mixed@example.test', 'role' => 'Moderator'],
        'local_password_hash' => Hash::make('Initial-123!')]);
    $directory = Mockery::mock(KeycloakUserDirectory::class);
    $directory->shouldReceive('user')->with('known-id')->andReturn(['username' => 'mixedcase', 'firstName' => 'New', 'lastName' => 'User', 'email' => 'mixed@example.test']);
    $directory->shouldReceive('role')->with('known-id')->andReturn('Moderator');
    $directory->shouldReceive('hasClientRole')->with('known-id', 'Moderator')->andReturn(true);
    $this->app->instance(KeycloakUserDirectory::class, $directory);
    app(KeycloakUserCreationService::class)->finish($sync);
    expect(User::where('keycloak_id', 'known-id')->sole()->username)->toBe('mixedcase')->and($sync->fresh()->status)->toBe('synced');
});
