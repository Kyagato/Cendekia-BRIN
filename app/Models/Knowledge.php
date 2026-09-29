<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Knowledge extends Model
{
    protected $table = 'knowledge'; 
    protected $guarded = ['id'];

    protected $casts = [
        'unggulan' => 'boolean',
        'views_count' => 'integer',
        'tanggal_terbit' => 'date',
    ];

    // Relasi ke Tag/Label
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // Relasi ke Kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke User (Pembuat)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke ForumThread
    public function threads()
    {
        return $this->hasMany(ForumThread::class, 'knowledge_id');
    }

    /**
     * Local Scope untuk memfilter data pengetahuan secara dinamis.
     * Mengeliminasi duplikasi query di HomeController, SearchController, dan ApiSearch.
     */
    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        // 1. Pencarian teks bebas (q)
        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('kolaborator', 'like', "%{$search}%")
                  ->orWhereHas('tags', fn ($tagQ) => $tagQ->where('nama_label', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn ($userQ) => $userQ->where('name', 'like', "%{$search}%")->orWhere('instansi', 'like', "%{$search}%"))
                  ->orWhereHas('category', fn ($catQ) => $catQ->where('nama_kategori', 'like', "%{$search}%"));
            });
        }

        // 2. Filter Tipe Konten (Teks, Video, Gambar, Audio)
        if (!empty($filters['tipe'])) {
            $query->where('tipe', $filters['tipe']);
        }

        // 3. Filter Kategori (category_id)
        if (!empty($filters['kategori'])) {
            $query->where('category_id', $filters['kategori']);
        }

        // 4. Filter Label / Tag
        if (!empty($filters['label'])) {
            $query->whereHas('tags', fn ($q) => $q->where('nama_label', $filters['label']));
        }

        // 5. Filter Instansi Pembuat
        if (!empty($filters['instansi'])) {
            $query->whereHas('user', fn ($q) => $q->where('instansi', $filters['instansi']));
        }

        // 6. Sorting / Pengurutan
        $sort = $filters['sort'] ?? 'terbaru';
        match ($sort) {
            'terpopuler' => $query->orderByDesc('views_count'),
            'az'         => $query->orderBy('judul', 'asc'),
            'za'         => $query->orderByDesc('judul'),
            default      => $query->latest(),
        };

        return $query;
    }
}
