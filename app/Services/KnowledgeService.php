<?php

namespace App\Services;

use App\Models\Knowledge;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KnowledgeService
{
    /**
     * Membersihkan cache statistik saat ada mutasi data pengetahuan.
     */
    public function invalidateStatsCache(): void
    {
        Cache::forget('stats_global_aggregation');
        Cache::forget('stats_instansi_list');
    }

    /**
     * Memeriksa apakah user memiliki hak otomatis disetujui (Admin atau Analis).
     */
    public function isAutoApproveUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->isAdmin() || $user->isAnalyst();
    }

    /**
     * Menyimpan pengetahuan baru dengan database transaction.
     */
    public function createKnowledge(array $data, ?User $user = null, ?UploadedFile $fileUpload = null, ?UploadedFile $audioFile = null): Knowledge
    {
        $user = $user ?? auth()->user();
        $newFiles = [];

        try {
            return DB::transaction(function () use ($data, $fileUpload, $audioFile, $user, &$newFiles) {
                $filePath = null;
                $urlTeks = $data['url_teks'] ?? null;
                $tipe = $data['tipe'] ?? 'Teks';

                // Penanganan unggahan berkas berdasarkan tipe media
                if ($tipe === 'Audio') {
                    $audioPath = null;
                    if ($audioFile) {
                        $audioPath = $audioFile->store('uploads', 'public');
                        $newFiles[] = $audioPath;
                    }

                    if ($fileUpload) {
                        // Ada thumbnail gambar yang diunggah
                        $filePath = $fileUpload->store('uploads', 'public');
                        $newFiles[] = $filePath;
                        $urlTeks = $audioPath;
                    } else {
                        // Tanpa thumbnail gambar terpisah, simpan path audio di file_path
                        $filePath = $audioPath;
                    }
                } else {
                    if ($fileUpload) {
                        $filePath = $fileUpload->store('uploads', 'public');
                        $newFiles[] = $filePath;
                    }
                }

                // Tentukan status persetujuan
                $statusInput = $data['status'] ?? 'Diajukan';
                if ($statusInput !== 'Draft') {
                    $status = $this->isAutoApproveUser($user) ? 'Disetujui' : 'Diajukan';
                } else {
                    $status = 'Draft';
                }

                $knowledge = Knowledge::create([
                    'user_id'        => $user->id,
                    'category_id'    => $data['category_id'],
                    'judul'          => $data['judul'],
                    'deskripsi'      => $data['deskripsi'] ?? null,
                    'detail'         => $data['detail'] ?? null,
                    'tanggal_terbit' => $data['tanggal_terbit'] ?? now(),
                    'status_akses'   => $data['status_akses'] ?? 'public',
                    'tipe'           => $tipe,
                    'file_path'      => $filePath,
                    'status'         => $status,
                    'penulis'        => $data['penulis'] ?? $user->name,
                    'kolaborator'    => $data['kolaborator'] ?? null,
                    'url_teks'       => $urlTeks,
                    'unggulan'       => !empty($data['unggulan']),
                ]);

                // Sinkronisasi Tag
                if (!empty($data['tags'])) {
                    $this->syncTags($knowledge, $data['tags']);
                }

                $this->invalidateStatsCache();

                \App\Models\AuditLog::record(
                    'KNOWLEDGE_CREATE',
                    "Membuat pengetahuan baru: '{$knowledge->judul}' (Tipe: {$knowledge->tipe}, Status: {$knowledge->status})",
                    ['knowledge_id' => $knowledge->id, 'judul' => $knowledge->judul, 'tipe' => $knowledge->tipe, 'status' => $knowledge->status],
                    $user
                );

                return $knowledge;
            });
        } catch (\Throwable $e) {
            foreach ($newFiles as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
            throw $e;
        }
    }

    /**
     * Memperbarui pengetahuan yang sudah ada dengan database transaction.
     */
    public function updateKnowledge(Knowledge $knowledge, array $data, ?User $user = null, ?UploadedFile $fileUpload = null, ?UploadedFile $audioFile = null): Knowledge
    {
        $user = $user ?? auth()->user();
        $newFiles = [];
        $oldFilesToDelete = [];

        try {
            $updated = DB::transaction(function () use ($knowledge, $data, $fileUpload, $audioFile, $user, &$newFiles, &$oldFilesToDelete) {
                $tipe = $data['tipe'] ?? $knowledge->tipe;

                $wasRejected = ($knowledge->status === Knowledge::STATUS_DITOLAK);

                // Tentukan status baru
                if (!empty($data['status']) && $data['status'] === Knowledge::STATUS_DRAFT) {
                    $newStatus = Knowledge::STATUS_DRAFT;
                } elseif ($this->isAutoApproveUser($user)) {
                    $newStatus = Knowledge::STATUS_DISETUJUI;
                } else {
                    $newStatus = in_array($knowledge->status, [Knowledge::STATUS_DRAFT, Knowledge::STATUS_DITOLAK])
                        ? Knowledge::STATUS_DIAJUKAN
                        : $knowledge->status;
                }

                $updateData = [
                    'category_id'    => $data['category_id'],
                    'judul'          => $data['judul'],
                    'deskripsi'      => $data['deskripsi'] ?? null,
                    'detail'         => $data['detail'] ?? null,
                    'tanggal_terbit' => $data['tanggal_terbit'] ?? $knowledge->tanggal_terbit,
                    'status_akses'   => $data['status_akses'] ?? 'public',
                    'tipe'           => $tipe,
                    'status'         => $newStatus,
                    'penulis'        => $data['penulis'] ?? null,
                    'kolaborator'    => $data['kolaborator'] ?? null,
                    'url_teks'       => $data['url_teks'] ?? null,
                    'unggulan'       => !empty($data['unggulan']),
                ];

                // Jika status disetujui atau diajukan kembali dari status ditolak, bersihkan catatan penolakan
                if ($newStatus === Knowledge::STATUS_DISETUJUI || $wasRejected) {
                    $updateData['catatan_penolakan'] = null;
                }

                if ($tipe === 'Audio') {
                    $audioPath = $knowledge->url_teks;
                    $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma', 'mp4', 'webm', 'mpga'];

                    if (!$audioPath && $knowledge->file_path && in_array(strtolower(pathinfo($knowledge->file_path, PATHINFO_EXTENSION)), $audioExtensions)) {
                        $audioPath = $knowledge->file_path;
                    }

                    if ($audioFile) {
                        if ($knowledge->url_teks && Storage::disk('public')->exists($knowledge->url_teks)) {
                            $oldFilesToDelete[] = $knowledge->url_teks;
                        }
                        $audioPath = $audioFile->store('uploads', 'public');
                        $newFiles[] = $audioPath;
                    }

                    if ($fileUpload) {
                        if ($knowledge->file_path && $knowledge->file_path !== $audioPath && Storage::disk('public')->exists($knowledge->file_path)) {
                            $oldFilesToDelete[] = $knowledge->file_path;
                        }
                        $updateData['file_path'] = $fileUpload->store('uploads', 'public');
                        $newFiles[] = $updateData['file_path'];
                        $updateData['url_teks'] = $audioPath;
                    } else {
                        if ($knowledge->file_path && $knowledge->file_path !== $audioPath) {
                            $updateData['url_teks'] = $audioPath;
                        } else {
                            $updateData['file_path'] = $audioPath;
                        }
                    }
                } else {
                    if ($fileUpload) {
                        if ($knowledge->file_path && Storage::disk('public')->exists($knowledge->file_path)) {
                            $oldFilesToDelete[] = $knowledge->file_path;
                        }
                        $updateData['file_path'] = $fileUpload->store('uploads', 'public');
                        $newFiles[] = $updateData['file_path'];
                    }
                }

                $knowledge->update($updateData);

                // Sinkronisasi Tag
                if (isset($data['tags'])) {
                    $this->syncTags($knowledge, $data['tags']);
                }

                $this->invalidateStatsCache();

                $auditLogMessage = "Memperbarui pengetahuan: '{$knowledge->judul}' (ID: {$knowledge->id})";
                if ($wasRejected) {
                    $auditLogMessage .= " (Diajukan kembali setelah perbaikan penolakan, Status: {$knowledge->status})";
                }

                \App\Models\AuditLog::record(
                    'KNOWLEDGE_UPDATE',
                    $auditLogMessage,
                    ['knowledge_id' => $knowledge->id, 'judul' => $knowledge->judul, 'status' => $knowledge->status, 'resubmitted' => $wasRejected],
                    $user
                );

                return $knowledge;
            });

            // Hapus file lama hanya jika transaksi database sukses
            foreach ($oldFilesToDelete as $oldFile) {
                if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            return $updated;
        } catch (\Throwable $e) {
            foreach ($newFiles as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
            throw $e;
        }
    }

    /**
     * Menghapus artikel pengetahuan secara lembut (Soft Delete). File fisik tetap tersimpan untuk pemulihan.
     */
    public function deleteKnowledge(Knowledge $knowledge): bool
    {
        return DB::transaction(function () use ($knowledge) {
            $knowledgeId = $knowledge->id;
            $knowledgeTitle = $knowledge->judul;

            $deleted = $knowledge->delete();

            $this->invalidateStatsCache();

            \App\Models\AuditLog::record(
                'KNOWLEDGE_DELETE',
                "Menghapus pengetahuan ke tong sampah: '{$knowledgeTitle}' (ID: {$knowledgeId})",
                ['knowledge_id' => $knowledgeId, 'judul' => $knowledgeTitle]
            );

            return (bool) $deleted;
        });
    }

    /**
     * Memulihkan artikel pengetahuan dari tong sampah (Restore).
     */
    public function restoreKnowledge(Knowledge $knowledge): bool
    {
        return DB::transaction(function () use ($knowledge) {
            $knowledgeId = $knowledge->id;
            $knowledgeTitle = $knowledge->judul;

            $restored = $knowledge->restore();

            $this->invalidateStatsCache();

            \App\Models\AuditLog::record(
                'KNOWLEDGE_RESTORE',
                "Memulihkan pengetahuan dari tong sampah: '{$knowledgeTitle}' (ID: {$knowledgeId})",
                ['knowledge_id' => $knowledgeId, 'judul' => $knowledgeTitle]
            );

            return (bool) $restored;
        });
    }

    /**
     * Menghapus artikel pengetahuan secara permanen beserta file fisik dan orphan tags.
     */
    public function forceDeleteKnowledge(Knowledge $knowledge): bool
    {
        return DB::transaction(function () use ($knowledge) {
            $knowledgeId = $knowledge->id;
            $knowledgeTitle = $knowledge->judul;

            // Hapus file fisik jika ada
            if ($knowledge->file_path && Storage::disk('public')->exists($knowledge->file_path)) {
                Storage::disk('public')->delete($knowledge->file_path);
            }
            if ($knowledge->url_teks && Storage::disk('public')->exists($knowledge->url_teks)) {
                Storage::disk('public')->delete($knowledge->url_teks);
            }
            if ($knowledge->gambar_sampul && Storage::disk('public')->exists($knowledge->gambar_sampul)) {
                Storage::disk('public')->delete($knowledge->gambar_sampul);
            }

            $tags = $knowledge->tags;
            $knowledge->tags()->detach();
            $deleted = $knowledge->forceDelete();

            // Hapus orphan tags
            foreach ($tags as $tag) {
                if ($tag->knowledge()->count() === 0) {
                    $tag->delete();
                }
            }

            $this->invalidateStatsCache();

            \App\Models\AuditLog::record(
                'KNOWLEDGE_FORCE_DELETE',
                "Menghapus permanen pengetahuan: '{$knowledgeTitle}' (ID: {$knowledgeId})",
                ['knowledge_id' => $knowledgeId, 'judul' => $knowledgeTitle]
            );

            return (bool) $deleted;
        });
    }

    /**
     * Memproses teks tag ("spbe, panduan" atau array) menjadi ID relasi tag di database.
     */
    protected function syncTags(Knowledge $knowledge, array|string|null $tags): void
    {
        if (empty($tags)) {
            $knowledge->tags()->detach();
            return;
        }

        if (is_string($tags)) {
            $tagNames = array_map('trim', explode(',', $tags));
        } else {
            $tagNames = array_map('trim', $tags);
        }

        $tagIds = [];

        foreach ($tagNames as $tagName) {
            if ($tagName !== '') {
                $tag = Tag::firstOrCreate(['nama_label' => strtolower($tagName)]);
                $tagIds[] = $tag->id;
            }
        }

        $knowledge->tags()->sync($tagIds);
    }
}
