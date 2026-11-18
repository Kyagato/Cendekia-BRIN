<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class AuditLog extends Model
{
    use HasFactory, MassPrunable;

    public const RETENTION_DAYS = 30;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User yang melakukan aktivitas.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menentukan query catatan audit log yang sudah melewati masa retensi (lebih dari 30 hari).
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(self::RETENTION_DAYS));
    }

    /**
     * Menghapus seluruh catatan audit log yang berusia lebih dari batas hari retensi.
     */
    public static function pruneExpired(int $days = self::RETENTION_DAYS): int
    {
        return static::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Membatasi query hanya untuk audit log dalam N hari terakhir.
     */
    public function scopeRecent(Builder $query, int $days = self::RETENTION_DAYS): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Helper cepat untuk mencatat aktivitas ke audit log.
     */
    public static function record(string $action, string $description, ?array $properties = null, ?User $user = null): ?self
    {
        try {
            $currentUser = $user ?? request()?->user() ?? auth()->user();

            $log = self::create([
                'user_id'     => $currentUser?->id,
                'action'      => strtoupper(trim($action)),
                'description' => $description,
                'ip_address'  => request()?->ip() ?? '127.0.0.1',
                'user_agent'  => request()?->userAgent(),
                'properties'  => $properties === null ? null : self::redactSecrets($properties),
            ]);

            if (Cache::add('audit_logs_auto_pruned', true, now()->addHours(6))) {
                self::pruneExpired();
            }

            return $log;
        } catch (\Throwable $e) {
            // Jangan sampai kegagalan logging menghentikan alur utama aplikasi
            \Illuminate\Support\Facades\Log::error('Gagal mencatat audit log.', ['action' => strtoupper(trim($action))]);
            return null;
        }
    }

    private static function redactSecrets(array $values): array
    {
        $sensitive = ['password', 'current_password', 'new_password', 'password_confirmation', 'password_hash', 'local_password_hash', 'token', 'access_token', 'refresh_token', 'id_token', 'client_secret', 'secret', 'otp', 'authorization', 'credentials', 'api_key', 'private_key'];
        foreach ($values as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitive, true)) {
                $values[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $values[$key] = self::redactSecrets($value);
            }
        }
        return $values;
    }

    /**
     * Helper badge warna kategori aksi untuk tampilan UI Blade.
     */
    public function getActionBadgeClassAttribute(): string
    {
        $action = strtoupper($this->action);

        if (str_contains($action, 'LOGIN') || str_contains($action, 'CREATE') || str_contains($action, 'APPROVE')) {
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-300 dark:border-emerald-800';
        }

        if (str_contains($action, 'UPDATE') || str_contains($action, 'EDIT')) {
            return 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border-blue-300 dark:border-blue-800';
        }

        if (str_contains($action, 'DELETE') || str_contains($action, 'REJECT') || str_contains($action, 'FAILED')) {
            return 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-300 dark:border-rose-800';
        }

        if (str_contains($action, 'LOGOUT') || str_contains($action, 'LOCK') || str_contains($action, 'PIN')) {
            return 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-300 dark:border-amber-800';
        }

        return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-300 dark:border-slate-700';
    }
}
