<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function updateBanner(Request $request): RedirectResponse
    {
        $request->validate([
            'banner' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $oldPath = $user->banner_path;
        $newPath = $request->file('banner')->store('profile_banners', 'public');

        try {
            $user->banner_path = $newPath;
            $user->save();
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($newPath);
            throw $exception;
        }

        if ($oldPath) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
        }

        return Redirect::route('users.show', $user)->with('success', 'Banner profil berhasil diperbarui.');
    }

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

        AuditLog::record(
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

        AuditLog::record(
            'API_TOKEN_REVOKED',
            "Super Admin '{$user->name}' ({$user->email}) mencabut Sanctum API token Swagger.",
            ['token_name' => self::SWAGGER_TOKEN_NAME, 'user_id' => $user->id]
        );

        return Redirect::route('users.show', $user)->with('profile_tab', 'apitoken');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, \App\Services\PasswordSyncService $passwords): RedirectResponse
    {
        $user = $request->user();
        
        // Exclude role from fill and handle it separately
        $validatedData = $request->validated();
        $role = $validatedData['role'] ?? null;
        unset($validatedData['role']);
        
        // Handle password update if filled
        $newPassword = $validatedData['password'] ?? null;
        unset($validatedData['password']);

        $user->fill($validatedData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        // Handle photo removal
        $oldPhoto = $user->foto_profil;
        $newPhoto = null;
        if ($request->boolean('remove_foto_profil') || $request->input('remove_foto_profil') == '1') {
            $user->foto_profil = null;
        }

        // Handle profile photo upload
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('profile_photos', 'public');
            $newPhoto = $path;
            $user->foto_profil = $path;
        }

        // Only Super Admin, Admin Pusat, Admin can change roles
        if ($role && $user->isSuperAdmin() && in_array($role, \App\Models\User::ALL_ROLES, true)) {
            $user->role = $role;
        }

        try {
            if ($newPassword) {
                $passwords->update($user, $newPassword);
                $request->session()->regenerate();
            } else {
                $user->save();
            }
        } catch (\Throwable $exception) {
            if ($newPhoto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($newPhoto);
            }
            throw $exception;
        }

        if ($oldPhoto && $oldPhoto !== $user->foto_profil) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPhoto);
        }

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
            ->publiclyVisible()
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(9);

        $totalKnowledge = $user->knowledge()->publiclyVisible()->count();
        $totalViews = (int) ($user->knowledge()->publiclyVisible()->sum('views_count') ?? 0);

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

        $currentUser = auth()->user();
        $isOwner = $currentUser && $currentUser->id === $user->id;
        $canSeePrivateData = $isOwner || ($currentUser && $currentUser->isAdmin());

        $bookmarkedList = null;
        $totalBookmarks = 0;

        if ($isOwner) {
            $bookmarkedList = $user->bookmarkedKnowledge()
                ->visibleTo($currentUser)
                ->where('status', 'Disetujui')
                ->with(['category', 'tags'])
                ->latest('bookmarks.created_at')
                ->paginate(9);
            $totalBookmarks = $user->bookmarkedKnowledge()->visibleTo($currentUser)->where('status', 'Disetujui')->count();
        }

        // Batasi data pengguna yang dikirim ke halaman profil publik agar tidak membocorkan PII (email, no_telepon, keycloak_id)
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'instansi' => $user->instansi,
            'pekerjaan' => $user->pekerjaan,
            'alamat' => $user->alamat,
            'foto_profil' => $user->foto_profil,
            'banner_path' => $user->banner_path,
            'created_at' => $user->created_at,
        ];

        if ($canSeePrivateData) {
            $userData['email'] = $user->email;
            $userData['no_telepon'] = $user->no_telepon;
        }

        return \Inertia\Inertia::render('Profile/Show', [
            'user' => $userData,
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
