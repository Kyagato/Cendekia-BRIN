<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    protected $table = 'forum_replies';
    protected $guarded = ['id'];

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
        return $this->hasMany(ForumReply::class, 'parent_id')->with('user', 'replies');
    }

    // Parent reply (if this is a nested reply)
    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }
}
