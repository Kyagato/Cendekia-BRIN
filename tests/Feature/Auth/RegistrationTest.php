<?php

test('registration redirects to keycloak sso registration', function () {
    $response = $this->get('/register');

    $response->assertRedirect();
});
