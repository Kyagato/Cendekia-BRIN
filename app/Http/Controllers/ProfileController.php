<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): \Inertia\Response
    {
        return \Inertia\Inertia::render('Profile/Edit', [
            'user' => $request->user(),
            'status' => session('status'),
        ]);
    }

    private const SWAGGER_TOKEN_NAME = 'swagger-token';

    /**
     * Generate API Token (Sanctum) untuk Swagger — khusus Super Admin.
     * Token lama dengan nama yang sama akan dicabut.
     */
    public function generateApiToken(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat membuat API Token.');

        $user->tokens()->where('name', self::SWAGGER_TOKEN_NAME)->delete();
        $token = $user->createToken(self::SWAGGER_TOKEN_NAME)->plainTextToken;

        \App\Models\AuditLog::record(
            'API_TOKEN_GENERATED',
            "Super Admin '{$user->name}' ({$user->email}) membuat Sanctum API token baru untuk Swagger.",
            ['token_name' => self::SWAGGER_TOKEN_NAME, 'user_id' => $user->id]
        );

        return Redirect::route('users.show', $user)
            ->with('api_token', $token)
            ->with('profile_tab', 'apitoken');
    }

    /**
     * Cabut (revoke) API Token Swagger milik Super Admin.
     */
    public function revokeApiToken(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        $user->tokens()->where('name', self::SWAGGER_TOKEN_NAME)->delete();

        \App\Models\AuditLog::record(
            'API_TOKEN_REVOKED',
            "Super Admin '{$user->name}' ({$user->email}) mencabut Sanctum API token Swagger.",
            ['token_name' => self::SWAGGER_TOKEN_NAME, 'user_id' => $user->id]
        );

        return Redirect::route('users.show', $user)->with('profile_tab', 'apitoken');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Exclude role from fill and handle it separately
        $validatedData = $request->validated();
        $role = $validatedData['role'] ?? null;
        unset($validatedData['role']);
        
        // Handle password update if filled
        if (!empty($validatedData['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validatedData['password']);
        }
        unset($validatedData['password']);

        $user->fill($validatedData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        // Handle photo removal
        if ($request->boolean('remove_foto_profil') || $request->input('remove_foto_profil') == '1') {
            if ($user->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto_profil)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = null;
        }

        // Handle profile photo upload
        if ($request->hasFile('foto_profil')) {
            // Delete old photo if exists
            if ($user->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto_profil)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profile_photos', 'public');
            $user->foto_profil = $path;
        }

        // Only Super Admin, Admin Pusat, Admin can change roles
        if ($role && $user->isAdmin()) {
            $user->role = $role;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (empty($user->keycloak_id)) {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the public profile of a user (POV Pengunjung & User Lain).
     */
    public function show(\App\Models\User $user): \Inertia\Response
    {
        // Hanya ambil pengetahuan yang berstatus 'Disetujui' (terbit publik)
        $knowledgeList = $user->knowledge()
            ->where('status', 'Disetujui')
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(9);

        $totalKnowledge = $user->knowledge()->where('status', 'Disetujui')->count();
        $totalViews = (int) ($user->knowledge()->where('status', 'Disetujui')->sum('views_count') ?? 0);

        // Thread forum yang dibuat oleh user ini
        $forumThreads = \App\Models\ForumThread::where('user_id', $user->id)
            ->approved()
            ->withCount('replies')
            ->with('category')
            ->latest()
            ->take(6)
            ->get();
        $totalThreads = \App\Models\ForumThread::where('user_id', $user->id)
            ->approved()
            ->count();

        // Daftar Bookmark Pengetahuan (hanya diambil jika user melihat profil miliknya sendiri)
        $isOwner = auth()->check() && auth()->id() === $user->id;
        $bookmarkedList = null;
        $totalBookmarks = 0;

        if ($isOwner) {
            $bookmarkedList = $user->bookmarkedKnowledge()
                ->where('status', 'Disetujui')
                ->with(['category', 'tags'])
                ->latest('bookmarks.created_at')
                ->paginate(9);
            $totalBookmarks = $user->bookmarkedKnowledge()->where('status', 'Disetujui')->count();
        }

        return \Inertia\Inertia::render('Profile/Show', [
            'user' => $user,
            'knowledgeList' => $knowledgeList,
            'totalKnowledge' => $totalKnowledge,
            'totalViews' => $totalViews,
            'forumThreads' => $forumThreads,
            'totalThreads' => $totalThreads,
            'bookmarkedList' => $bookmarkedList,
            'totalBookmarks' => $totalBookmarks,
            'canManageApiToken' => $canManageApiToken = $isOwner && $user->isSuperAdmin(),
            // Token plain text hanya tersedia sekali (flash) setelah digenerate
            'apiToken' => $canManageApiToken ? session('api_token') : null,
            'apiTokenInfo' => $canManageApiToken
                ? $user->tokens()->where('name', self::SWAGGER_TOKEN_NAME)->latest()->first(['id', 'created_at', 'last_used_at'])
                : null,
            'initialTab' => $canManageApiToken ? session('profile_tab') : null,
        ]);
    }
}
