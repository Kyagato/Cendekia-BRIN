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

        // Only Super Admin, Admin Pusat, Admin IPPD can change roles
        if ($role && in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD'])) {
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
            $request->validate([
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
            ->where('status', 'approved')
            ->withCount('replies')
            ->with('category')
            ->latest()
            ->take(6)
            ->get();
        $totalThreads = \App\Models\ForumThread::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        return \Inertia\Inertia::render('Profile/Show', [
            'user' => $user,
            'knowledgeList' => $knowledgeList,
            'totalKnowledge' => $totalKnowledge,
            'totalViews' => $totalViews,
            'forumThreads' => $forumThreads,
            'totalThreads' => $totalThreads,
        ]);
    }
}
