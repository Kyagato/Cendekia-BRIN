<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('allows super admin to login via api and obtain sanctum token', function () {
    $user = User::factory()->create([
        'email' => 'superadmin@brin.go.id',
        'password' => Hash::make('admin123'),
        'role' => User::ROLE_SUPER_ADMIN,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'superadmin@brin.go.id',
        'password' => 'admin123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'token',
            'user' => ['id', 'name', 'email', 'role'],
        ])
        ->assertJson([
            'status' => 'success',
            'user' => [
                'email' => 'superadmin@brin.go.id',
                'role' => User::ROLE_SUPER_ADMIN,
            ],
        ]);

    $token = $response->json('token');

    // Test accessing protected endpoint using the token
    $protectedResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/user');

    $protectedResponse->assertStatus(200)
        ->assertJson([
            'email' => 'superadmin@brin.go.id',
        ]);
});

it('rejects default password when superadmin has no local password', function () {
    $user = User::factory()->create([
        'email' => 'superadmin@brin.go.id',
        'password' => null,
        'role' => User::ROLE_SUPER_ADMIN,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'superadmin@brin.go.id',
        'password' => 'admin123',
    ]);

    $response->assertStatus(401);
    expect($user->fresh()->password)->toBeNull();
});

it('rejects invalid password on api login', function () {
    User::factory()->create([
        'email' => 'superadmin@brin.go.id',
        'password' => Hash::make('admin123'),
        'role' => User::ROLE_SUPER_ADMIN,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'superadmin@brin.go.id',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'status' => 'error',
            'message' => 'Email atau password salah.',
        ]);
});
