<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    const CREATED_AT = 'date';
    const UPDATED_AT = null;

    protected $fillable = [
        'type',
        'method',
        'email',
        'amount',
        'status',
        'phone',
        'payout_address',
        'details',
        'RecAmount',
        'fx_rate',
        'tracking_id',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'RecAmount' => 'decimal:2',
        'fx_rate'   => 'decimal:4',
        'date'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
