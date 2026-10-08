<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        $hasKeycloak = !empty(config('services.keycloak.base_url')) && !empty(config('services.keycloak.client_id'));

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($hasKeycloak) {
            try {
                $clientId = config('services.keycloak.client_id');
                $keycloakLogoutUrl = \Laravel\Socialite\Facades\Socialite::driver('keycloak')
                    ->getLogoutUrl(url('/'), $clientId);

                if ($request->header('X-Inertia') || $request->wantsJson()) {
                    return Inertia::location($keycloakLogoutUrl);
                }

                return redirect($keycloakLogoutUrl);
            } catch (\Exception $e) {
                return redirect('/');
            }
        }

        return redirect('/');
    }
}
