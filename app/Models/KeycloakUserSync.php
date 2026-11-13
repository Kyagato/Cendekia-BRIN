<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeycloakUserSync extends Model
{
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    protected $guarded = ['id'];

    protected $hidden = ['local_password_hash'];

    protected $casts = ['before' => 'array', 'after' => 'array', 'remote_confirmed' => 'boolean', 'local_password_hash' => 'encrypted'];
}
