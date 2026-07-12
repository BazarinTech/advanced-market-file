<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'email', 'package', 'daily', 'totals', 'price',
        'status', 'cycle', 'amount', 'earnings', 'last_claimed_at',
    ];

    protected $casts = [
        'daily'           => 'decimal:2',
        'totals'          => 'decimal:2',
        'price'           => 'decimal:2',
        'amount'          => 'decimal:2',
        'earnings'        => 'decimal:2',
        'last_claimed_at' => 'datetime',
    ];

    public function canClaim(): bool
    {
        if (is_null($this->last_claimed_at)) {
            return true;
        }
        $nextClaim = $this->last_claimed_at->copy()->addDay()->setTime(9, 0, 0);
        return now()->gte($nextClaim);
    }

    public function nextClaimAt(): \Carbon\Carbon
    {
        return $this->last_claimed_at->copy()->addDay()->setTime(9, 0, 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
