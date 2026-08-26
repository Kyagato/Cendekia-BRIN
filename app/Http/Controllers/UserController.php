<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get the list of roles the current admin is allowed to manage.
     */
    private function getAllowedRoles(): array
    {
        $user = auth()->user();

        if ($user->role === 'Super Admin' || $user->email === 'superadmin@brin.go.id') {
            // Super Admin can manage ALL roles (including other Super Admins)
            return ['Super Admin', 'Admin Pusat', 'Admin IPPD', 'Anggota', 'Analisis Pengetahuan', 'Moderator'];
        }

        if ($user->role === 'Admin Pusat') {
            // Admin Pusat can manage: Admin IPPD, Anggota, Moderator, Analisis Pengetahuan
            return ['Admin IPPD', 'Anggota', 'Analisis Pengetahuan', 'Moderator'];
        }

        if ($user->role === 'Admin IPPD') {
            // Admin IPPD can manage: Anggota, Moderator, Analisis Pengetahuan
            return ['Anggota', 'Analisis Pengetahuan', 'Moderator'];
        }

        return [];
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $allowedRoles = $this->getAllowedRoles();
        $user = auth()->user();

        $query = User::query();

        // Non-Super Admin only see users with roles they can manage
        if ($user->role !== 'Super Admin' && $user->email !== 'superadmin@brin.go.id') {
            $query->whereIn('role', $allowedRoles);
        }

        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('instansi', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $allowedRolesForView = $allowedRoles;

        return view('admin.users.index', compact('users', 'allowedRolesForView'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $allowedRoles = $this->getAllowedRoles();
        return view('admin.users.form', compact('allowedRoles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $allowedRoles = $this->getAllowedRoles();
        $allowedRolesString = implode(',', $allowedRoles);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'gender' => 'required|in:L,P',
            'instansi' => 'nullable|string|max:255',
            'role' => "required|string|in:{$allowedRolesString}",
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoProfilPath = null;
        if ($request->hasFile('foto_profil')) {
            $fotoProfilPath = $request->file('foto_profil')->store('profile_photos', 'public');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'jenis_kelamin' => $validated['gender'],
            'instansi' => $validated['instansi'],
            'role' => $validated['role'],
            'foto_profil' => $fotoProfilPath,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $allowedRoles = $this->getAllowedRoles();

        // Prevent editing users outside of allowed roles (except Super Admin can edit everyone)
        $currentUser = auth()->user();
        if ($currentUser->role !== 'Super Admin' && $currentUser->email !== 'superadmin@brin.go.id') {
            if (!in_array($user->role, $allowedRoles)) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengedit pengguna ini.');
            }
        }

        return view('admin.users.form', compact('user', 'allowedRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $allowedRoles = $this->getAllowedRoles();
        $allowedRolesString = implode(',', $allowedRoles);

        // Prevent updating users outside of allowed roles
        $currentUser = auth()->user();
        if ($currentUser->role !== 'Super Admin' && $currentUser->email !== 'superadmin@brin.go.id') {
            if (!in_array($user->role, $allowedRoles)) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengedit pengguna ini.');
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8',
            'gender' => 'required|in:L,P',
            'instansi' => 'nullable|string|max:255',
            'role' => "required|string|in:{$allowedRolesString}",
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->jenis_kelamin = $validated['gender'];
        $user->instansi = $validated['instansi'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = $request->file('foto_profil')->store('profile_photos', 'public');
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Prevent deleting users outside of allowed roles
        $allowedRoles = $this->getAllowedRoles();
        $currentUser = auth()->user();
        if ($currentUser->role !== 'Super Admin' && $currentUser->email !== 'superadmin@brin.go.id') {
            if (!in_array($user->role, $allowedRoles)) {
                return redirect()->route('admin.users.index')->with('error', 'Anda tidak memiliki hak akses untuk menghapus pengguna ini.');
            }
        }

        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
