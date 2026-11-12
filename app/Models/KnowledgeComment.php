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
        'mention_user',
        'konten',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(KnowledgeCommentVote::class);
    }

    public function scopeWithVoteSummary($query)
    {
        return $query->withCount([
            'votes as upvotes' => fn ($q) => $q->where('value', 1),
            'votes as downvotes' => fn ($q) => $q->where('value', -1),
        ])->withSum([
            'votes as user_vote' => fn ($q) => $q->where('user_id', auth()->id()),
        ], 'value');
    }

    public function scopeSortedForDisplay($query, $sort = 'default')
    {
        $sort = in_array($sort, ['default', 'newest', 'oldest'], true) ? $sort : 'default';
        $query->withVoteSummary()->withCount('replies')->reorder();
        if ($sort === 'default') {
            $query->orderByDesc('upvotes')->orderByDesc('replies_count');
        }
        $direction = $sort === 'oldest' ? 'asc' : 'desc';

        return $query->orderBy('created_at', $direction)->orderBy('id', $direction);
    }

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
        return $this->hasMany(KnowledgeComment::class, 'parent_id')->withVoteSummary()
            ->with(['user', 'replies.user'])
            ->oldest();
    }
}
