<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function __construct(private ReferralService $referralService) {}

    public function deposits()
    {
        $deposits = \App\Models\Transaction::where('type', 'Deposits')
            ->orWhere('type', 'Deposit')
            ->orderByDesc('ID')
            ->paginate(25)->onEachSide(1)->withQueryString();

        return Inertia::render('Admin/Deposits', compact('deposits'));
    }

    public function approveDeposit(Request $request, $id)
    {
        $tx = \App\Models\Transaction::findOrFail($id);

        abort_unless($tx->status === 'Pending' && $tx->method === 'crypto', 400);

        $earnings = \App\Models\Earning::where('email', $tx->email)->firstOrFail();
        $earnings->balance += $tx->amount;
        $earnings->deposit += $tx->amount;
        $earnings->save();

        $tx->status = 'Success';
        $tx->save();

        \App\Models\User::where('email', $tx->email)->update(['status' => 'Active']);

        $this->referralService->payCommission($tx->email, (float) $tx->amount);

        return back()->with('success', 'Crypto deposit approved and credited.');
    }

    public function rejectDeposit(Request $request, $id)
    {
        $tx = \App\Models\Transaction::findOrFail($id);

        abort_unless($tx->status === 'Pending' && $tx->method === 'crypto', 400);

        $tx->status = 'Rejected';
        $tx->save();

        return back()->with('success', 'Crypto deposit rejected.');
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

        return Inertia::render('Admin/Withdrawals', compact('withdrawals'));
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
