<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'amount', 'daily', 'days', 'image', 'active', 'one_time_only', 'tasks_per_day', 'task_category'];

    protected $appends = ['image_url'];

    protected $casts = [
        'amount'        => 'decimal:2',
        'daily'         => 'decimal:2',
        'active'        => 'boolean',
        'one_time_only' => 'boolean',
    ];

    public function getTotalAttribute(): float
    {
        return $this->daily * $this->days;
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image ? Media::url('packages/'.$this->image) : null,
        );
    }
}
