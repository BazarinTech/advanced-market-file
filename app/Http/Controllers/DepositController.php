<?php

namespace App\Http\Controllers;

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
        return Inertia::render('Dashboard/Deposit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'phone'  => 'required|string|max:20',
        ]);

        $user   = Auth::user();
        $amount = (float) $request->amount;
        $phone  = $request->phone;

        // Normalise phone — Palpluss expects format like 0712345678
        if (str_starts_with($phone, '+254')) {
            $phone = '0' . substr($phone, 4);
        }

        // Generate unique tracking ID
        $trackingId = 'DEP-' . strtoupper(Str::random(10)) . '-' . time();

        // Insert Pending transaction BEFORE calling Palpluss
        Transaction::create([
            'email'       => $user->email,
            'phone'       => $phone,
            'amount'      => $amount,
            'type'        => 'Deposit',
            'status'      => 'Pending',
            'details'     => '',
            'RecAmount'   => 0,
            'tracking_id' => $trackingId,
        ]);

        // Initiate STK push — tracking ID sent as accountReference
        $result = $this->palpluss->initiateDeposit($amount, $phone, $trackingId);

        if ($result['status'] === 'Success') {
            return back()->with('success', $result['message']);
        }

        // STK push failed — mark transaction as Failed
        Transaction::where('tracking_id', $trackingId)->update(['status' => 'Failed']);

        return back()->with('error', $result['message']);
    }
}
