<?php

use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;

test('forgot password screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('otp code can be requested', function () {
    Mail::fake();

    $user = User::factory()->create();

    $response = $this->post('/forgot-password', ['email' => $user->email]);

    Mail::assertSent(SendOtpMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });

    $response->assertRedirect(route('password.otp.show'));
    expect(session('reset_email'))->toBe($user->email);
    expect(session('reset_otp'))->not->toBeEmpty();
});

test('otp code can be verified', function () {
    Mail::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);
    $otp = session('reset_otp');

    $response = $this->post('/forgot-password/verify', ['otp' => $otp]);

    $response->assertRedirect(route('password.otp.reset'));
    expect(session('otp_verified'))->toBeTrue();
});

test('password can be reset after otp verification', function () {
    Mail::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);
    $otp = session('reset_otp');
    $this->post('/forgot-password/verify', ['otp' => $otp]);

    $response = $this->post('/forgot-password/reset', [
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ]);

    $response->assertRedirect(route('login'));
    expect(\Illuminate\Support\Facades\Hash::check('new-password123', $user->fresh()->password))->toBeTrue();
});
