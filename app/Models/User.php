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
        'no_telepon',
        'pekerjaan',
        'jenis_kelamin',
        'alamat',
        'instansi',
        'role',
        'foto_profil',
        'dark_mode',
        'keycloak_id',
        'email_verified_at',
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

    // ============================================================
    // STANDAR KONSTANTA ROLE RESMI
    // ============================================================
    public const ROLE_SUPER_ADMIN = 'Super Admin';
    public const ROLE_ADMIN_PUSAT  = 'Admin Pusat';
    public const ROLE_ADMIN        = 'Admin';
    public const ROLE_ANALIS       = 'Analisis Pengetahuan';
    public const ROLE_MODERATOR    = 'Moderator';
    public const ROLE_ANGGOTA      = 'Anggota';

    public const ALL_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN_PUSAT,
        self::ROLE_ADMIN,
        self::ROLE_ANGGOTA,
        self::ROLE_ANALIS,
        self::ROLE_MODERATOR,
    ];

    public const ADMIN_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN_PUSAT,
        self::ROLE_ADMIN,
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdminPusat(): bool
    {
        return $this->role === self::ROLE_ADMIN_PUSAT;
    }

    public function isAdminRegular(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->role === 'Admin IPPD';
    }

    public function isAdminIPPD(): bool
    {
        return $this->isAdminRegular();
    }

    public function isAnalyst(): bool
    {
        return $this->role === self::ROLE_ANALIS || $this->role === 'Analis Pengetahuan';
    }

    public function isModerator(): bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }

    public function isMember(): bool
    {
        return in_array($this->role, [self::ROLE_ANGGOTA, 'Kreator Pengetahuan']);
    }

    public function isCreator(): bool
    {
        return $this->isMember();
    }

    public function isGuest(): bool
    {
        return false;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, self::ADMIN_ROLES);
    }

    public function canManageContent(): bool
    {
        return $this->isAdmin() || in_array($this->role, [
            self::ROLE_ANGGOTA,
            self::ROLE_ANALIS,
            self::ROLE_MODERATOR,
        ]);
    }
}