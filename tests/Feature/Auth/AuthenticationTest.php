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
