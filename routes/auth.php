<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [\App\Http\Controllers\Auth\KeycloakController::class, 'register'])
        ->name('register');

    Route::post('register', [\App\Http\Controllers\Auth\KeycloakController::class, 'register']);

    Route::get('login', [\App\Http\Controllers\Auth\KeycloakController::class, 'redirect'])
        ->name('login');

    Route::post('login', [\App\Http\Controllers\Auth\KeycloakController::class, 'redirect']);

    // Keycloak SSO Routes
    Route::get('auth/keycloak/redirect', [\App\Http\Controllers\Auth\KeycloakController::class, 'redirect'])
        ->name('keycloak.redirect');
    Route::get('auth/keycloak/register', [\App\Http\Controllers\Auth\KeycloakController::class, 'register'])
        ->name('keycloak.register');
    Route::get('auth/keycloak/callback', [\App\Http\Controllers\Auth\KeycloakController::class, 'callback'])
        ->name('keycloak.callback');

    // Flow Lupa Password & Kode Autentikasi 4 Digit (OTP)
    Route::get('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'showEmailForm'])
        ->name('password.request');

    Route::post('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'sendOtp'])
        ->name('password.email')
        ->middleware('throttle:auth-otp');

    Route::get('forgot-password/verify', [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'showOtpForm'])
        ->name('password.otp.show');

    Route::post('forgot-password/verify', [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'verifyOtp'])
        ->name('password.otp.verify')
        ->middleware('throttle:auth-otp');

    Route::get('forgot-password/reset', [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'showResetForm'])
        ->name('password.otp.reset');

    Route::post('forgot-password/reset', [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'updatePassword'])
        ->name('password.otp.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
