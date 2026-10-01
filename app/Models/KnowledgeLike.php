<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_id',
        'user_id',
    ];

    public function knowledge(): BelongsTo
    {
        return $this->belongsTo(Knowledge::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
