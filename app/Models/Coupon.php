<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'amount',
        'expires_at',
        'max_uses',
        'used_count',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'expires_at' => 'datetime',
        'max_uses'   => 'integer',
        'used_count' => 'integer',
    ];

    public function uses()
    {
        return $this->hasMany(CouponUse::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->isAfter($this->expires_at);
    }

    public function isExhausted(): bool
    {
        return $this->max_uses > 0 && $this->used_count >= $this->max_uses;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isExhausted();
    }

    public function alreadyUsedBy(string $email): bool
    {
        return $this->uses()->where('user_email', $email)->exists();
    }

    public function minutesLeft(): int
    {
        if (!$this->expires_at || $this->isExpired()) return 0;
        return (int) now()->diffInMinutes($this->expires_at);
    }
}
