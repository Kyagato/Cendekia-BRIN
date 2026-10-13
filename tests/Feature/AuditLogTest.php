<?php

use App\Models\AuditLog;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('records an audit log correctly', function () {
    $user = User::factory()->create([
        'role' => 'Super Admin',
    ]);

    $log = AuditLog::record('TEST_ACTION', 'Mencoba tes pencatatan log', ['key' => 'value'], $user);

    expect($log)->not->toBeNull();
    expect($log->action)->toBe('TEST_ACTION');
    expect($log->description)->toBe('Mencoba tes pencatatan log');
    expect($log->user_id)->toBe($user->id);
    expect($log->properties)->toBe(['key' => 'value']);
});

it('records USER_ROLE_CHANGED audit log when role is updated via API', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $target = User::factory()->create(['role' => User::ROLE_ANGGOTA]);

    Sanctum::actingAs($admin);

    $response = $this->putJson("/api/admin/users/{$target->id}/role", [
        'role' => User::ROLE_ADMIN,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data' => [
                'old_role' => User::ROLE_ANGGOTA,
                'new_role' => User::ROLE_ADMIN,
            ],
        ]);

    $target->refresh();
    expect($target->role)->toBe(User::ROLE_ADMIN);

    $log = AuditLog::where('action', 'USER_ROLE_CHANGED')
        ->where('user_id', $admin->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->properties['target_user_id'])->toBe($target->id);
    expect($log->properties['old_role'])->toBe(User::ROLE_ANGGOTA);
    expect($log->properties['new_role'])->toBe(User::ROLE_ADMIN);
});

it('does not record new audit log or update DB when role is unchanged in API', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $target = User::factory()->create(['role' => User::ROLE_ANGGOTA]);

    Sanctum::actingAs($admin);

    $logCountBefore = AuditLog::count();

    $response = $this->putJson("/api/admin/users/{$target->id}/role", [
        'role' => User::ROLE_ANGGOTA,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Role tidak berubah.',
            'data' => [
                'old_role' => User::ROLE_ANGGOTA,
                'new_role' => User::ROLE_ANGGOTA,
                'keycloak_synced' => false,
            ],
        ]);

    expect(AuditLog::count())->toBe($logCountBefore);
});

it('records API_TOKEN_GENERATED audit log when super admin generates Swagger token', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    $response = $this->actingAs($superAdmin)->post(route('profile.api-token.generate'));

    $response->assertRedirect(route('users.show', $superAdmin));
    $response->assertSessionHas('api_token');

    $log = AuditLog::where('action', 'API_TOKEN_GENERATED')
        ->where('user_id', $superAdmin->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->properties['token_name'])->toBe('swagger-token');
    expect($log->properties['user_id'])->toBe($superAdmin->id);
});

it('records API_TOKEN_REVOKED audit log when super admin revokes Swagger token', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    // Buat token terlebih dahulu
    $superAdmin->createToken('swagger-token');

    $response = $this->actingAs($superAdmin)->delete(route('profile.api-token.revoke'));

    $response->assertRedirect(route('users.show', $superAdmin));

    $log = AuditLog::where('action', 'API_TOKEN_REVOKED')
        ->where('user_id', $superAdmin->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->properties['token_name'])->toBe('swagger-token');
    expect($superAdmin->tokens()->where('name', 'swagger-token')->count())->toBe(0);
});

it('forbids non-super admin from generating or revoking api token', function () {
    $regularUser = User::factory()->create(['role' => 'Member']);

    $this->actingAs($regularUser)->post(route('profile.api-token.generate'))
        ->assertStatus(403);

    $this->actingAs($regularUser)->delete(route('profile.api-token.revoke'))
        ->assertStatus(403);

    expect(AuditLog::whereIn('action', ['API_TOKEN_GENERATED', 'API_TOKEN_REVOKED'])->count())->toBe(0);
});

it('does not trust spoofed X-Forwarded-For header when proxies are not configured', function () {
    $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

    $response = $this->actingAs($user)
        ->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
        ->withHeaders(['X-Forwarded-For' => '8.8.8.8'])
        ->post(route('profile.api-token.generate'));

    $log = AuditLog::where('action', 'API_TOKEN_GENERATED')
        ->where('user_id', $user->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->ip_address)->toBe('127.0.0.1');
    expect($log->ip_address)->not->toBe('8.8.8.8');
});

