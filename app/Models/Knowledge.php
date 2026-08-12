<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Knowledge extends Model
{
    // Karena nama tabel kita 'knowledge' (bukan knowledges), kita harus definisikan secara eksplisit
    protected $table = 'knowledge'; 
    protected $guarded = ['id'];
    protected $casts = [
        'validated_at' => 'datetime',
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

    // Relasi ke User (Validator / Analisis Pengetahuan)
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}

