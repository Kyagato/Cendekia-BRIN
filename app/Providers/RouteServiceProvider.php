<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Rate Limiter untuk Login API (maksimal 5 percobaan per menit per email/IP)
        RateLimiter::for('api-login', function (Request $request) {
            $key = (string) $request->input('email') . '|' . $request->ip();
            return Limit::perMinute(5)->by($key)->response(function () {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak percobaan login. Silakan tunggu 1 menit sebelum mencoba kembali.',
                ], 429);
            });
        });

        // Rate Limiter untuk Pencarian & Autocomplete (maksimal 30 request per menit per IP)
        RateLimiter::for('search-limit', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip())->response(function () {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak permintaan pencarian. Silakan perlambat jeda pencarian Anda (Maksimal 30 request/menit).',
                ], 429);
            });
        });

        // Rate Limiter untuk Permintaan OTP Lupa Password (maksimal 3 percobaan per menit per email/IP)
        RateLimiter::for('auth-otp', function (Request $request) {
            $key = (string) $request->input('email', $request->ip());
            return Limit::perMinute(3)->by($key)->response(function () {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak permintaan pengiriman kode OTP. Silakan tunggu 1 menit.',
                ], 429);
            });
        });

        // Rate Limiter Global untuk API Publik (maksimal 60 request per menit per IP/Token)
        RateLimiter::for('api-public', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();
            return Limit::perMinute(60)->by($key)->response(function () {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Batas kuota akses API terlampaui (60 request/menit). Silakan tunggu sejenak.',
                ], 429);
            });
        });
    }
}
