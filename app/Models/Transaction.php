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
        'email',
        'amount',
        'status',
        'phone',
        'details',
        'RecAmount',
        'tracking_id',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'RecAmount' => 'decimal:2',
        'date'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
