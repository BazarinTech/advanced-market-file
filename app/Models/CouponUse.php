<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUse extends Model
{
    protected $table = 'coupon_uses';

    public $timestamps = false;

    protected $fillable = [
        'coupon_id',
        'user_email',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
