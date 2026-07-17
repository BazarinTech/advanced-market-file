<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Services\PalplussService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DepositController extends Controller
{
    public function __construct(private PalplussService $palpluss) {}

    public function show()
    {
        return Inertia::render('Dashboard/Deposit', [
            'cryptoAddress' => Setting::get('crypto_deposit_address'),
            'depositMin'    => (float) Setting::get('deposit_min', 1),
        ]);
    }

    public function store(Request $request)
    {
        $minAmount = (float) Setting::get('deposit_min', 1);

        $request->validate([
            'method' => 'required|in:mpesa,crypto',
            'amount' => "required|numeric|min:{$minAmount}",
            'phone'  => 'required_if:method,mpesa|nullable|string|max:20',
        ]);

        $user   = Auth::user();
        $rate   = (float) Setting::get('usd_kes_rate', 130);
        $usd    = (float) $request->amount;
        $kes    = round($usd * $rate, 2);

        if ($request->method === 'crypto') {
            Transaction::create([
                'email'     => $user->email,
                'method'    => 'crypto',
                'amount'    => $kes,
                'type'      => 'Deposit',
                'status'    => 'Pending',
                'details'   => '',
                'RecAmount' => 0,
                'fx_rate'   => $rate,
            ]);

            return back()->with('success', 'Your crypto deposit request has been received and will be reviewed within ~30 minutes.');
        }

        $phone = $request->phone;

        // Normalise phone — Palpluss expects format like 0712345678
        if (str_starts_with($phone, '+254')) {
            $phone = '0' . substr($phone, 4);
        }

        // Generate unique tracking ID
        $trackingId = 'DEP-' . strtoupper(Str::random(10)) . '-' . time();

        // Insert Pending transaction BEFORE calling Palpluss
        Transaction::create([
            'email'       => $user->email,
            'method'      => 'mpesa',
            'phone'       => $phone,
            'amount'      => $kes,
            'type'        => 'Deposit',
            'status'      => 'Pending',
            'details'     => '',
            'RecAmount'   => 0,
            'fx_rate'     => $rate,
            'tracking_id' => $trackingId,
        ]);

        // Initiate STK push — tracking ID sent as accountReference
        $result = $this->palpluss->initiateDeposit($kes, $phone, $trackingId);

        if ($result['status'] === 'Success') {
            return back()->with('success', $result['message']);
        }

        // STK push failed — mark transaction as Failed
        Transaction::where('tracking_id', $trackingId)->update(['status' => 'Failed']);

        return back()->with('error', $result['message']);
    }
}
