<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = ['id'];

    // Relasi kembali ke Knowledge
    public function knowledge()
    {
        return $this->belongsToMany(Knowledge::class);
    }
}

