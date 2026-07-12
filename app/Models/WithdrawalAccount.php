<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalAccount extends Model
{
    protected $fillable = ['email', 'phone', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
