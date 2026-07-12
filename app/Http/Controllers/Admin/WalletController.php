<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $query = Earning::with('user');

        if ($search = $request->input('search')) {
            $query->where('email', 'like', "%{$search}%");
        }

        $wallets = $query->orderByDesc('balance')->paginate(20)->onEachSide(1)->withQueryString();

        return Inertia::render('Admin/Wallets', compact('wallets'));
    }

    public function edit(Earning $wallet)
    {
        $wallet->load('user');
        return Inertia::render('Admin/WalletEdit', compact('wallet'));
    }

    public function update(Request $request, Earning $wallet)
    {
        $request->validate([
            'balance'  => 'required|numeric|min:0',
            'withdraw' => 'required|numeric|min:0',
            'referral' => 'required|numeric|min:0',
            'bonus'    => 'required|numeric|min:0',
            'deposit'  => 'required|numeric|min:0',
        ]);

        $wallet->update([
            'balance'  => $request->balance,
            'withdraw' => $request->withdraw,
            'referral' => $request->referral,
            'bonus'    => $request->bonus,
            'deposit'  => $request->deposit,
        ]);

        return redirect()->route('admin.wallets')->with('success', "Wallet for {$wallet->email} updated successfully.");
    }
}
