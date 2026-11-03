<?php

use App\Models\User;

test('login redirects to keycloak sso', function () {
    $response = $this->get('/login');

    $response->assertRedirect();
});

test('keycloak sso redirect route initiates redirect', function () {
    $response = $this->get(route('keycloak.redirect'));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('prompt=login');
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect();
});

test('users can logout via inertia request without type error', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout', [], ['X-Inertia' => 'true']);

    $this->assertGuest();
    // Inertia::location returns a 409 Conflict with X-Inertia-Location header, or a redirect if Keycloak is not configured
    expect($response->status())->toBeIn([409, 302]);
});

test('keycloak callback creates new user with password without class not found error', function () {
    $abstractUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
    $abstractUser->shouldReceive('getId')->andReturn('kc-uuid-12345');
    $abstractUser->shouldReceive('getName')->andReturn('Keycloak Test User');
    $abstractUser->shouldReceive('getNickname')->andReturn('kctest');
    $abstractUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
    $abstractUser->shouldReceive('getRaw')->andReturn([
        'sub' => 'kc-uuid-12345',
        'email' => 'newuser@example.com',
        'name' => 'Keycloak Test User',
        'realm_access' => ['roles' => ['Anggota']],
    ]);
    $abstractUser->user = [
        'sub' => 'kc-uuid-12345',
        'email' => 'newuser@example.com',
        'name' => 'Keycloak Test User',
        'realm_access' => ['roles' => ['Anggota']],
    ];

    $provider = Mockery::mock(\Laravel\Socialite\Two\AbstractProvider::class);
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($abstractUser);

    \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')->with('keycloak')->andReturn($provider);

    $response = $this->get('/auth/keycloak/callback?code=mock_code');

    $response->assertRedirect('/');
    $user = User::where('email', 'newuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->password)->not->toBeNull();
});

test('keycloak callback maps analis role correctly to Analisis Pengetahuan', function () {
    $abstractUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
    $abstractUser->shouldReceive('getId')->andReturn('kc-uuid-analis');
    $abstractUser->shouldReceive('getName')->andReturn('Analyst User');
    $abstractUser->shouldReceive('getNickname')->andReturn('analis');
    $abstractUser->shouldReceive('getEmail')->andReturn('analis@simpan.brin');
    $abstractUser->shouldReceive('getRaw')->andReturn([
        'sub' => 'kc-uuid-analis',
        'email' => 'analis@simpan.brin',
        'realm_access' => ['roles' => ['Analis Pengetahuan', 'default-roles-mojopedia']],
    ]);
    $abstractUser->user = [
        'sub' => 'kc-uuid-analis',
        'email' => 'analis@simpan.brin',
    ];
    $abstractUser->token = 'header.' . base64_encode(json_encode([
        'realm_access' => ['roles' => ['Analis Pengetahuan']],
    ])) . '.sig';

    $provider = Mockery::mock(\Laravel\Socialite\Two\AbstractProvider::class);
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($abstractUser);

    \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')->with('keycloak')->andReturn($provider);

    $response = $this->get('/auth/keycloak/callback?code=mock_code');

    $response->assertRedirect('/');
    $user = User::where('email', 'analis@simpan.brin')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe(User::ROLE_ANALIS);
});



