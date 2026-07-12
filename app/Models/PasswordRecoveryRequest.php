<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordRecoveryRequest extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'network', 'status', 'resolved_at'];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];
}
