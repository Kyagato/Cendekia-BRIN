<?php

use App\Models\User;
use App\Models\Knowledge;

it('prevents an admin from promoting their own role through profile', function () {
    $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->actingAs($user)->patch('/profile', [
        'name' => $user->name, 'email' => $user->email, 'role' => User::ROLE_SUPER_ADMIN,
    ])->assertSessionHasNoErrors();
    expect($user->fresh()->role)->toBe(User::ROLE_ADMIN);
});

it('denies anonymous access to approved private knowledge', function () {
    $knowledge = new Knowledge(['status' => 'Disetujui', 'status_akses' => 'private']);
    expect($knowledge->canBeViewedBy(null))->toBeFalse();
});

it('rejects expired reset codes', function () {
    $this->withSession(['reset_email' => 'test@example.com', 'reset_otp' => '1234',
        'reset_otp_expires' => now()->subMinute()->timestamp])
        ->post('/forgot-password/verify', ['otp' => '1234'])->assertRedirect('/forgot-password');
    expect(session()->has('otp_verified'))->toBeFalse();
});

it('rejects reset codes after the attempt limit even with a different email input', function () {
    $this->withSession(['reset_email' => 'test@example.com', 'reset_otp' => '1234',
        'reset_otp_expires' => now()->addMinutes(5)->timestamp, 'reset_otp_attempts' => 5])
        ->post('/forgot-password/verify', ['otp' => '1234', 'email' => 'different@example.com'])
        ->assertRedirect('/forgot-password');
    expect(session()->has('otp_verified'))->toBeFalse();
});
