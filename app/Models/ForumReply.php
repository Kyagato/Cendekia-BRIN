<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{
    use SoftDeletes;

    protected $table = 'forum_replies';
    protected $guarded = ['id'];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_best_answer' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    // Self-referencing: a reply can have nested child replies
    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'parent_id')
            ->with('user', 'replies')
            ->orderByDesc('is_pinned')
            ->oldest();
    }

    // Parent reply (if this is a nested reply)
    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }
}
