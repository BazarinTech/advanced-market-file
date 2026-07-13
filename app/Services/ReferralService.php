<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;

class ReferralService
{
    private const MAX_LEVELS = 3;

    public function payCommission(string $depositorEmail, float $kesAmount): void
    {
        $rates = [
            1 => (float) Setting::get('referral_level1_pct', 10),
            2 => (float) Setting::get('referral_level2_pct', 3),
            3 => (float) Setting::get('referral_level3_pct', 1),
        ];

        $current = User::where('email', $depositorEmail)->first();

        for ($level = 1; $level <= self::MAX_LEVELS && $current; $level++) {
            if (!$current->refer) {
                break;
            }

            $sponsor = User::find($current->refer);
            if (!$sponsor || !$sponsor->earnings) {
                break;
            }

            $commission = $kesAmount * ($rates[$level] / 100);

            if ($commission > 0) {
                $sponsor->earnings->balance  += $commission;
                $sponsor->earnings->referral += $commission;
                $sponsor->earnings->save();

                Transaction::create([
                    'email'     => $sponsor->email,
                    'phone'     => $sponsor->phone,
                    'amount'    => $commission,
                    'type'      => 'Referral',
                    'status'    => 'Success',
                    'details'   => "Level {$level} commission from {$depositorEmail}",
                    'RecAmount' => $commission,
                ]);
            }

            $current = $sponsor;
        }
    }
}
