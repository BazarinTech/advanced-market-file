<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawalAccount;
use App\Services\PalplussService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WithdrawController extends Controller
{
    public function __construct(private PalplussService $palpluss) {}

    public function show()
    {
        $user     = auth()->user();
        $accounts = WithdrawalAccount::where('email', $user->email)->get()->keyBy('method');

        return Inertia::render('Dashboard/Withdraw', [
            'accounts'           => [
                'mpesa'  => $accounts->get('mpesa'),
                'crypto' => $accounts->get('crypto'),
            ],
            'withdrawal_min'     => (float) Setting::get('withdrawal_min', 50),
            'withdrawal_fee'     => (float) Setting::get('withdrawal_fee', 5),
            'eligibleToWithdraw' => $this->isEligibleToWithdraw($user),
        ]);
    }

    /**
     * A user may withdraw once they've made a deposit themselves, or once at
     * least one member of their downline (any of the 3 referral levels) has —
     * blocks withdrawing task earnings on an account that never funded the
     * platform, directly or via a referral.
     */
    private function isEligibleToWithdraw(User $user): bool
    {
        if ((float) ($user->earnings->deposit ?? 0) > 0) {
            return true;
        }

        $level1 = User::where('refer', $user->ID)->pluck('ID');
        $level2 = $level1->isEmpty() ? collect() : User::whereIn('refer', $level1)->pluck('ID');
        $level3 = $level2->isEmpty() ? collect() : User::whereIn('refer', $level2)->pluck('ID');
        $downlineIds = $level1->concat($level2)->concat($level3);

        if ($downlineIds->isEmpty()) {
            return false;
        }

        $downlineEmails = User::whereIn('ID', $downlineIds)->pluck('email');

        return Earning::whereIn('email', $downlineEmails)->where('deposit', '>', 0)->exists();
    }

    public function store(Request $request)
    {
        $rate       = (float) Setting::get('usd_kes_rate', 130);
        $minAmount  = (float) Setting::get('withdrawal_min', 50);
        $feePercent = (float) Setting::get('withdrawal_fee', 5);
        $minUsd     = round($minAmount / $rate, 2);

        $request->validate([
            'method' => 'required|in:mpesa,crypto',
            'amount' => "required|numeric|min:{$minUsd}",
        ]);

        $user    = auth()->user();
        $account = WithdrawalAccount::where('email', $user->email)->where('method', $request->method)->first();

        if (!$account) {
            return back()->with('error', 'Please set up your withdrawal account first.');
        }

        if (!$this->isEligibleToWithdraw($user)) {
            return back()->with('error', 'You need to make a deposit, or have a referral who has deposited, before you can withdraw.');
        }

        $earnings = $user->earnings;
        $usd      = (float) $request->amount;
        $amount   = round($usd * $rate, 2);
        $balance  = (float) $earnings->balance;

        if ($amount > $balance) {
            // The USD amount shown/entered is itself a rounded (to the nearest
            // cent) conversion of the real KES balance, so converting it back to
            // KES can overshoot the true balance by up to half a cent's worth of
            // KES — most commonly when withdrawing (at or near) the full balance
            // or the minimum. Clamp rather than falsely reject if the overshoot
            // is just that rounding noise (a full cent's worth of KES, for a
            // comfortable margin either way the rate is set).
            if ($amount - $balance <= $rate * 0.01) {
                $amount = $balance;
            } else {
                return back()->with('error', 'Insufficient balance to process this withdrawal request.');
            }
        }

        $fee       = $amount * ($feePercent / 100);
        $netAmount = round($amount - $fee, 2);
        $reference = 'WD-' . strtoupper(Str::random(8)) . '-' . time();

        // Deduct balance immediately
        $earnings->balance  -= $amount;
        $earnings->withdraw += $amount;
        $earnings->save();

        if ($request->method === 'crypto') {
            Transaction::create([
                'email'          => $user->email,
                'method'         => 'crypto',
                'payout_address' => $account->crypto_address,
                'amount'         => $amount,
                'type'           => 'Withdraw',
                'status'         => 'Pending',
                'details'        => $reference,
                'RecAmount'      => $netAmount,
                'fx_rate'        => $rate,
                'tracking_id'    => $reference,
            ]);

            return back()->with('success', 'Your crypto withdrawal request has been received and will be processed within ~30 minutes.');
        }

        // Record as Pending
        Transaction::create([
            'email'       => $user->email,
            'method'      => 'mpesa',
            'phone'       => $account->phone,
            'amount'      => $amount,
            'type'        => 'Withdraw',
            'status'      => 'Pending',
            'details'     => $reference,
            'RecAmount'   => $netAmount,
            'fx_rate'     => $rate,
            'tracking_id' => $reference,
        ]);

        // Initiate auto payout via Palpluss B2C
        $result = $this->palpluss->initiatePayout($netAmount, $account->phone, $reference);

        if ($result['status'] !== 'Success') {
            // Payout API failed — keep balance deducted and leave tx as Pending for manual processing
            return back()->with('success', "Withdrawal request received. It will be sent to {$account->phone} within 24 hours.");
        }

        return back()->with('success', "Withdrawal initiated. It will be sent to {$account->phone} shortly.");
    }
}
