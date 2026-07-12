<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WithdrawalAccountController extends Controller
{
    public function index()
    {
        $accounts = WithdrawalAccount::orderByDesc('updated_at')
            ->paginate(25)->onEachSide(1)->withQueryString();
        return Inertia::render('Admin/WithdrawalAccounts', compact('accounts'));
    }

    public function update(Request $request, WithdrawalAccount $account)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        $account->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Withdrawal account updated successfully.');
    }
}
