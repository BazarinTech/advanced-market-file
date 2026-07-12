<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'amount', 'daily', 'days', 'image', 'active'];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily'  => 'decimal:2',
        'active' => 'boolean',
    ];

    public function getTotalAttribute(): float
    {
        return $this->daily * $this->days;
    }

    public function imageUrl(): string
    {
        return asset('images/packages/' . $this->image);
    }
}
