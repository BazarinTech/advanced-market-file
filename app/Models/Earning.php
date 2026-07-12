<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    protected $table = 'earnings';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'email',
        'balance',
        'withdraw',
        'referral',
        'bonus',
        'deposit',
        'totals',
        'roll',
    ];

    protected $casts = [
        'balance'  => 'decimal:2',
        'withdraw' => 'decimal:2',
        'referral' => 'decimal:2',
        'bonus'    => 'decimal:2',
        'deposit'  => 'decimal:2',
        'totals'   => 'decimal:2',
        'roll'     => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
