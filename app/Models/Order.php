<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'email', 'package', 'daily', 'totals', 'price',
        'status', 'cycle', 'amount', 'earnings', 'last_claimed_at',
        'tasks_per_day', 'task_category', 'tasks_claimed_today',
    ];

    protected $casts = [
        'daily'           => 'decimal:2',
        'totals'          => 'decimal:2',
        'price'           => 'decimal:2',
        'amount'          => 'decimal:2',
        'earnings'        => 'decimal:2',
        'last_claimed_at' => 'datetime',
    ];

    /**
     * The 9:00 AM boundary that "contains" the given instant — i.e. the most
     * recent 9:00 AM at or before it. A day's batch of tasks unlocks at this
     * boundary and stays open until the next one, 24 hours later.
     */
    protected function claimWindowFor(Carbon $instant): Carbon
    {
        $nineAm = $instant->copy()->setTime(9, 0, 0);
        return $instant->gte($nineAm) ? $nineAm : $nineAm->subDay();
    }

    /**
     * How many of today's batch of tasks have already been claimed. Resets
     * to 0 once the last claim's window has rolled over into a new one,
     * regardless of what's stored in tasks_claimed_today.
     */
    public function tasksClaimedToday(): int
    {
        if (is_null($this->last_claimed_at)) {
            return 0;
        }

        if ($this->claimWindowFor($this->last_claimed_at)->lt($this->claimWindowFor(now()))) {
            return 0;
        }

        return $this->tasks_claimed_today;
    }

    public function tasksRemainingToday(): int
    {
        return max(0, $this->tasks_per_day - $this->tasksClaimedToday());
    }

    public function canClaim(): bool
    {
        return $this->tasksRemainingToday() > 0;
    }

    /**
     * When the next batch opens, if today's is used up.
     */
    public function nextClaimAt(): Carbon
    {
        return $this->claimWindowFor($this->last_claimed_at ?? now())->copy()->addDay();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
