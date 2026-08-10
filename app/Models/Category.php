<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Mengizinkan semua kolom diisi secara massal kecuali 'id'
    protected $guarded = ['id'];

    // Relasi One-to-Many ke Knowledge
    public function knowledge()
    {
        return $this->hasMany(Knowledge::class);
    }
}

