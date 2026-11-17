<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuditLogController extends Controller
{
    private const SUCCESS_ACTIONS = [
        'LOGIN', 'LOGOUT', 'KNOWLEDGE_APPROVE', 'KNOWLEDGE_REJECT',
        'KEYCLOAK_USER_CREATE', 'KEYCLOAK_USER_UPDATE', 'KEYCLOAK_USER_DELETE',
        'KEYCLOAK_USER_CREATE_RECONCILED', 'KEYCLOAK_USER_DELETE_RECONCILED',
        'KEYCLOAK_USER_RECONCILED', 'KEYCLOAK_PASSWORD_UPDATE',
        'USER_ROLE_CHANGED', 'PASSWORD_UPDATE', 'PASSWORD_RESET_LOCAL',
    ];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => array_filter(['nullable', 'date_format:Y-m-d', $request->filled('from') ? 'after_or_equal:from' : null]),
            'action' => ['nullable', 'string', 'max:100'],
            'actor' => ['nullable', 'string', 'max:100'],
            'result' => ['nullable', Rule::in(['success', 'failed', 'recorded'])],
        ]);
        $query = AuditLog::query()->with('user:id,name,email');
        if (! empty($filters['from'])) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($filters['from'], 'Asia/Jakarta')->startOfDay()->utc());
        }
        if (! empty($filters['to'])) {
            $query->where('created_at', '<', \Carbon\Carbon::parse($filters['to'], 'Asia/Jakarta')->addDay()->startOfDay()->utc());
        }
        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        if (! empty($filters['actor'])) {
            $actor = $filters['actor'];
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.$actor.'%')->orWhere('email', 'like', '%'.$actor.'%'));
        }
        if (($filters['result'] ?? '') === 'success') {
            $query->whereIn('action', self::SUCCESS_ACTIONS);
        } elseif (($filters['result'] ?? '') === 'failed') {
            $query->where(fn ($q) => $q->where('action', 'like', '%FAILED%')->orWhere('action', 'like', '%CONFLICT%'));
        } elseif (($filters['result'] ?? '') === 'recorded') {
            $query->whereNotIn('action', self::SUCCESS_ACTIONS)->where('action', 'not like', '%FAILED%')->where('action', 'not like', '%CONFLICT%');
        }
        $logs = $query->orderByDesc('created_at')->orderByDesc('id')->paginate(20)->withQueryString();
        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action')
            ->filter(fn ($action) => ! str_starts_with(self::actionLabel($action), 'Aktivitas: '))
            ->sort(fn ($a, $b) => strcasecmp(self::actionLabel($a), self::actionLabel($b)) ?: strcmp($a, $b))
            ->values();

        return view('admin.audit.index', compact('logs', 'actions', 'filters'));
    }

    public static function actionLabel(string $action): string
    {
        return match ($action) {
            'LOGIN' => 'Masuk',
            'LOGOUT' => 'Keluar',
            'LOGIN_FAILED' => 'Gagal masuk',
            'PASSWORD_UPDATE' => 'Mengubah kata sandi profil',
            'PASSWORD_UPDATE_FAILED' => 'Gagal mengubah kata sandi profil',
            'PASSWORD_RESET_LOCAL' => 'Mengatur ulang kata sandi lokal',
            'USER_CREATE' => 'Menambah pengguna',
            'USER_UPDATE' => 'Mengubah pengguna',
            'USER_DELETE' => 'Menghapus pengguna',
            'USER_ROLE_CHANGED' => 'Mengubah peran pengguna',
            'KEYCLOAK_USER_CREATE' => 'Menambah pengguna Keycloak',
            'KEYCLOAK_USER_CREATE_FAILED' => 'Gagal menambah pengguna Keycloak',
            'KEYCLOAK_USER_UPDATE' => 'Mengubah pengguna Keycloak',
            'KEYCLOAK_USER_UPDATE_FAILED' => 'Gagal mengubah pengguna Keycloak',
            'KEYCLOAK_USER_DELETE' => 'Menghapus pengguna Keycloak',
            'KEYCLOAK_USER_DELETE_FAILED' => 'Gagal menghapus pengguna Keycloak',
            'KEYCLOAK_USER_SYNC_FAILED' => 'Gagal menyinkronkan pengguna Keycloak',
            'KEYCLOAK_USER_SYNC_CONFLICT' => 'Konflik sinkronisasi pengguna Keycloak',
            'KEYCLOAK_USER_CREATE_RECONCILED' => 'Menyelesaikan sinkronisasi pengguna baru Keycloak',
            'KEYCLOAK_USER_DELETE_RECONCILED' => 'Menyelesaikan sinkronisasi penghapusan pengguna Keycloak',
            'KEYCLOAK_USER_RECONCILED' => 'Menyelesaikan sinkronisasi pengguna Keycloak',
            'KEYCLOAK_PASSWORD_UPDATE' => 'Mengubah kata sandi pengguna Keycloak',
            'KEYCLOAK_PASSWORD_UPDATE_FAILED' => 'Gagal mengubah kata sandi pengguna Keycloak',
            'KEYCLOAK_PASSWORD_SYNC_FAILED' => 'Gagal menyinkronkan kata sandi Keycloak',
            'KEYCLOAK_PASSWORD_SYNC_CONFLICT' => 'Konflik sinkronisasi kata sandi Keycloak',
            'KEYCLOAK_PASSWORD_RECONCILED' => 'Menyelesaikan sinkronisasi kata sandi Keycloak',
            'KNOWLEDGE_CREATE' => 'Menambah pengetahuan',
            'KNOWLEDGE_CREATE_API' => 'Menambah pengetahuan melalui API',
            'KNOWLEDGE_UPDATE' => 'Mengubah pengetahuan',
            'KNOWLEDGE_DELETE' => 'Menghapus pengetahuan',
            'KNOWLEDGE_RESTORE' => 'Memulihkan pengetahuan',
            'KNOWLEDGE_FORCE_DELETE' => 'Menghapus permanen pengetahuan',
            'KNOWLEDGE_VALIDATE_UPDATE' => 'Mengubah pengetahuan saat validasi',
            'KNOWLEDGE_APPROVE' => 'Menyetujui pengetahuan',
            'KNOWLEDGE_REJECT' => 'Menolak pengetahuan',
            'KNOWLEDGE_LIKE' => 'Menyukai pengetahuan',
            'KNOWLEDGE_UNLIKE' => 'Membatalkan suka pengetahuan',
            'KNOWLEDGE_COMMENT' => 'Mengomentari pengetahuan',
            'KNOWLEDGE_COMMENT_UPDATE' => 'Mengubah komentar pengetahuan',
            'KNOWLEDGE_COMMENT_DELETE' => 'Menghapus komentar pengetahuan',
            'FORUM_CREATE' => 'Menambah topik forum',
            'FORUM_UPDATE' => 'Mengubah topik forum',
            'FORUM_DELETE' => 'Menghapus topik forum',
            'FORUM_RESTORE' => 'Memulihkan topik forum',
            'FORUM_FORCE_DELETE' => 'Menghapus permanen topik forum',
            'FORUM_REPLY_UPDATE' => 'Mengubah balasan forum',
            'FORUM_REPLY_DELETE' => 'Menghapus balasan forum',
            'FORUM_REPLY_PIN' => 'Mengubah sematan balasan forum',
            'FORUM_PIN' => 'Mengubah sematan topik forum',
            'FORUM_LOCK' => 'Mengubah kunci topik forum',
            'CATEGORY_CREATED' => 'Menambah kategori',
            'CATEGORY_UPDATED' => 'Mengubah kategori',
            'CATEGORY_DELETED' => 'Menghapus kategori',
            'FAQ_CREATE' => 'Menambah pertanyaan umum',
            'FAQ_UPDATE' => 'Mengubah pertanyaan umum',
            'FAQ_DELETE' => 'Menghapus pertanyaan umum',
            'FAQ_SECTION_UPDATE' => 'Mengubah bagian pertanyaan umum',
            'FAQ_SECTION_DELETE' => 'Menghapus bagian pertanyaan umum',
            'BOOKMARK_ADD' => 'Menambah penanda',
            'BOOKMARK_REMOVE' => 'Menghapus penanda',
            'API_TOKEN_GENERATED' => 'Membuat token API',
            'API_TOKEN_REVOKED' => 'Mencabut token API',
            default => 'Aktivitas: '.$action,
        };
    }

    public static function result(AuditLog $log): string
    {
        if (str_contains($log->action, 'FAILED') || str_contains($log->action, 'CONFLICT')) {
            return 'Gagal / perlu pemeriksaan';
        }

        return in_array($log->action, self::SUCCESS_ACTIONS, true) ? 'Berhasil' : 'Tercatat';
    }

    public static function details(AuditLog $log): array
    {
        // Only explicitly approved fields are displayed; never render arbitrary stored JSON.
        $properties = $log->properties ?? [];
        $allowed = ['target_user_id', 'created_user_id', 'knowledge_id', 'judul', 'keycloak_user_id', 'sync_id', 'sync_status', 'old_role', 'new_role'];
        $details = array_filter(array_intersect_key($properties, array_flip($allowed)), fn ($value) => is_scalar($value) || $value === null);
        foreach (['before', 'after'] as $side) {
            if (isset($properties[$side]) && is_array($properties[$side])) {
                $details[$side] = array_filter(array_intersect_key($properties[$side], array_flip(['username', 'firstName', 'lastName', 'email', 'emailVerified', 'role'])), fn ($value) => is_scalar($value) || $value === null);
            }
        }
        return $details;
    }
}
