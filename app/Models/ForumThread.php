<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    protected $table = 'forum_threads';
    protected $guarded = ['id'];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_pinned'   => 'boolean',
        'is_locked'   => 'boolean',
    ];

    // Roles that get auto-approval
    const AUTO_APPROVE_ROLES = ['super_admin', 'admin_pusat', 'admin_ippd', 'Super Admin', 'Admin Pusat', 'Admin IPPD'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    public function knowledge()
    {
        return $this->belongsTo(Knowledge::class, 'knowledge_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
