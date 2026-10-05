<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumThread extends Model
{
    use SoftDeletes;

    protected $table = 'forum_threads';
    protected $guarded = ['id'];

    public const STATUS_APPROVED = 'approved';
    public const STATUS_PENDING  = 'pending';
    public const STATUS_REJECTED = 'rejected';

    public const ALL_STATUSES = [
        self::STATUS_APPROVED,
        self::STATUS_PENDING,
        self::STATUS_REJECTED,
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_pinned'   => 'boolean',
        'is_locked'   => 'boolean',
    ];

    // Roles that get auto-approval: Super Admin, Admin Pusat, Admin, Moderator
    public const AUTO_APPROVE_ROLES = [
        User::ROLE_SUPER_ADMIN,
        User::ROLE_ADMIN_PUSAT,
        User::ROLE_ADMIN,
        User::ROLE_MODERATOR,
    ];

    public static function canAutoApprove(User $user): bool
    {
        return in_array($user->role, self::AUTO_APPROVE_ROLES);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

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
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}
