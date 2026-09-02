<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ForgotPasswordOtpController extends Controller
{
    /**
     * Step 1: Tampilkan Form Input Email Lupa Password
     */
    public function showEmailForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Step 1 Submit: Verifikasi Email & Kirim Kode Autentikasi 4 Digit (OTP) melalui Email
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Alamat email ini tidak terdaftar dalam sistem MojoPedia.',
        ]);

        // Generate 4-digit numeric OTP code
        $otp = str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

        session([
            'reset_email'  => $request->email,
            'reset_otp'    => $otp,
            'otp_verified' => false,
        ]);

        $user = User::where('email', $request->email)->first();

        // Kirim email berisi kode autentikasi 4 digit
        try {
            Mail::to($request->email)->send(new SendOtpMail($otp, $user->name ?? 'Pengguna'));
        } catch (\Exception $e) {
            // Log error jika pengiriman email bermasalah
            \Illuminate\Support\Facades\Log::error("Gagal mengirim email OTP: " . $e->getMessage());
        }

        return redirect()->route('password.otp.show')
            ->with('success_otp', "Kode autentikasi 4 digit telah dikirimkan ke email {$request->email}. Silakan periksa kotak masuk atau folder spam email Anda.");
    }

    /**
     * Step 2: Tampilkan Form Input Kode Autentikasi 4 Digit
     */
    public function showOtpForm(): View|RedirectResponse
    {
        if (!session()->has('reset_email') || !session()->has('reset_otp')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Silakan masukkan alamat email Anda terlebih dahulu.']);
        }

        return view('auth.verify-otp');
    }

    /**
     * Step 2 Submit: Validasi Kode Autentikasi 4 Digit
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|numeric|digits:4',
        ], [
            'otp.required' => 'Kode autentikasi 4 digit wajib diisi.',
            'otp.numeric'  => 'Kode autentikasi harus berupa 4 angka.',
            'otp.digits'   => 'Kode autentikasi harus persis 4 digit angka.',
        ]);

        $savedOtp = session('reset_otp');

        if ($request->otp != $savedOtp) {
            return back()->withErrors(['otp' => 'Kode autentikasi 4 digit yang Anda masukkan salah. Silakan periksa kembali.']);
        }

        session(['otp_verified' => true]);

        return redirect()->route('password.otp.reset')
            ->with('success', 'Kode autentikasi berhasil diverifikasi! Silakan masukkan password baru Anda.');
    }

    /**
     * Step 3: Tampilkan Form Reset Password Baru
     */
    public function showResetForm(): View|RedirectResponse
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi verifikasi telah berakhir. Silakan masukkan email dari awal.']);
        }

        return view('auth.reset-password-otp');
    }

    /**
     * Step 3 Submit: Simpan Password Baru & Bawa User Kembali ke Login
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi verifikasi telah berakhir. Silakan masukkan email dari awal.']);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Hapus data sesi reset password
        session()->forget(['reset_email', 'reset_otp', 'otp_verified']);

        return redirect()->route('login')
            ->with('success', 'Password Anda berhasil diperbarui! Silakan masuk menggunakan password baru Anda.');
    }
}
