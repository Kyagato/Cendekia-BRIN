<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_id',
        'user_id',
        'parent_id',
        'konten',
    ];

    public function knowledge(): BelongsTo
    {
        return $this->belongsTo(Knowledge::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(KnowledgeComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(KnowledgeComment::class, 'parent_id')->with('user')->latest();
    }
}
