<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
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
        $user    = auth()->user();
        $account = WithdrawalAccount::where('email', $user->email)->first();

        return Inertia::render('Dashboard/Withdraw', [
            'account'        => $account,
            'withdrawal_min' => (float) Setting::get('withdrawal_min', 50),
            'withdrawal_fee' => (float) Setting::get('withdrawal_fee', 5),
        ]);
    }

    public function setupAccount(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        $user = auth()->user();

        WithdrawalAccount::updateOrCreate(
            ['email' => $user->email],
            ['name' => $request->name, 'phone' => $request->phone]
        );

        return back()->with('success', 'Withdrawal account set up successfully.');
    }

    public function store(Request $request)
    {
        $minAmount  = (float) Setting::get('withdrawal_min', 50);
        $feePercent = (float) Setting::get('withdrawal_fee', 5);

        $request->validate([
            'amount' => "required|numeric|min:{$minAmount}",
        ]);

        $user    = auth()->user();
        $account = WithdrawalAccount::where('email', $user->email)->first();

        if (!$account) {
            return back()->with('error', 'Please set up your withdrawal account first.');
        }

        $earnings  = $user->earnings;
        $amount    = (float) $request->amount;

        if ($amount < $minAmount) {
            return back()->with('error', "Minimum withdrawal amount is Kes {$minAmount}.");
        }

        if ($amount > $earnings->balance) {
            return back()->with('error', 'Insufficient balance to process this withdrawal request.');
        }

        $fee       = $amount * ($feePercent / 100);
        $netAmount = round($amount - $fee, 2);
        $reference = 'WD-' . strtoupper(Str::random(8)) . '-' . time();

        // Deduct balance immediately
        $earnings->balance  -= $amount;
        $earnings->withdraw += $amount;
        $earnings->save();

        // Record as Pending
        $tx = Transaction::create([
            'email'       => $user->email,
            'phone'       => $account->phone,
            'amount'      => $amount,
            'type'        => 'Withdraw',
            'status'      => 'Pending',
            'details'     => $reference,
            'RecAmount'   => $netAmount,
            'tracking_id' => $reference,
        ]);

        // Initiate auto payout via Palpluss B2C
        $result = $this->palpluss->initiatePayout($netAmount, $account->phone, $reference);

        if ($result['status'] !== 'Success') {
            // Payout API failed — keep balance deducted and leave tx as Pending for manual processing
            return back()->with('success', "Withdrawal request received. Kes {$netAmount} will be sent to {$account->phone} within 24 hours.");
        }

        return back()->with('success', "Withdrawal initiated. Kes {$netAmount} will be sent to {$account->phone} shortly.");
    }
}
