<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;

class ReferralService
{
    private const COMMISSION_RATE = 0.10;

    public function payCommission(string $depositorEmail, float $kesAmount): void
    {
        $user = User::where('email', $depositorEmail)->first();
        if (!$user || !$user->refer) {
            return;
        }

        $sponsor = User::find($user->refer);
        if (!$sponsor || !$sponsor->earnings) {
            return;
        }

        $commission = $kesAmount * self::COMMISSION_RATE;
        $sponsor->earnings->balance  += $commission;
        $sponsor->earnings->referral += $commission;
        $sponsor->earnings->save();

        Transaction::create([
            'email'     => $sponsor->email,
            'phone'     => $sponsor->phone,
            'amount'    => $commission,
            'type'      => 'Referral',
            'status'    => 'Success',
            'details'   => 'Commission from ' . $depositorEmail,
            'RecAmount' => $commission,
        ]);
    }
}
