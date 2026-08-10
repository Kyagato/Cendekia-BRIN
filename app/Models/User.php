<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'jenis_kelamin',
        'instansi',
        'role',
        'foto_profil',
        'dark_mode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
// Relasi One-to-Many ke Knowledge (Konten yang diunggah oleh user ini)
    public function knowledge()
    {
        return $this->hasMany(Knowledge::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dark_mode' => 'boolean',
        ];
    }

    public function isSuperAdmin()
    {
        return $this->role === 'Super Admin';
    }

    public function isAdminPusat()
    {
        return $this->role === 'Admin Pusat';
    }

    public function isAdminIPPD()
    {
        return $this->role === 'Admin IPPD';
    }

    public function isCreator()
    {
        return $this->role === 'Kreator Pengetahuan';
    }

    public function isAnalyst()
    {
        return $this->role === 'Analisis Pengetahuan';
    }

    public function isModerator()
    {
        return $this->role === 'Moderator';
    }

    public function isMember()
    {
        return $this->role === 'Anggota';
    }

    public function isGuest()
    {
        return $this->role === 'Guest';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD']);
    }

    public function canManageContent()
    {
        return in_array($this->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD', 'Kreator Pengetahuan', 'Analisis Pengetahuan']);
    }
}
