<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function deposits()
    {
        $deposits = \App\Models\Transaction::where('type', 'Deposits')
            ->orWhere('type', 'Deposit')
            ->orderByDesc('ID')
            ->paginate(25)->onEachSide(1)->withQueryString();

        return view('admin.deposits', compact('deposits'));
    }

    public function manualDeposit(Request $request)
    {
        $request->validate([
            'email'         => 'required|email|exists:users,email',
            'phone'         => 'required|string',
            'amount'        => 'required|numeric|min:1',
            'transactionID' => 'required|string',
        ]);

        $earnings = \App\Models\Earning::where('email', $request->email)->firstOrFail();
        $earnings->balance  += $request->amount;
        $earnings->deposit  += $request->amount;
        $earnings->save();

        \App\Models\Transaction::create([
            'email'     => $request->email,
            'phone'     => $request->phone,
            'amount'    => $request->amount,
            'RecAmount' => $request->amount,
            'type'      => 'Deposits',
            'status'    => 'Success',
            'details'   => $request->transactionID,
        ]);

        return back()->with('success', 'Deposit processed successfully.');
    }

    public function withdrawals()
    {
        $withdrawals = \App\Models\Transaction::where('type', 'Withdraw')
            ->orderByDesc('ID')
            ->paginate(25)->onEachSide(1)->withQueryString();

        return view('admin.withdrawals', compact('withdrawals'));
    }

    public function approveWithdrawal(Request $request, $id)
    {
        $tx = \App\Models\Transaction::findOrFail($id);
        $tx->status = 'Approved';
        $tx->save();

        return back()->with('success', 'Withdrawal approved.');
    }

    public function rejectWithdrawal(Request $request, $id)
    {
        $tx       = \App\Models\Transaction::findOrFail($id);
        $earnings = \App\Models\Earning::where('email', $tx->email)->first();

        if ($earnings) {
            $earnings->balance   += $tx->amount;
            $earnings->withdraw  -= $tx->amount;
            $earnings->save();
        }

        $tx->status = 'Rejected';
        $tx->save();

        return back()->with('success', 'Withdrawal rejected and funds returned.');
    }
}
