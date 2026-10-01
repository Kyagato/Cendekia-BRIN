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

