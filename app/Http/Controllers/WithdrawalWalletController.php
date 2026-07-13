<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalAccount;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalWalletController extends Controller
{
    public function sendCode(Request $request, SmsService $sms)
    {
        $request->validate([
            'method' => 'required|in:mpesa,crypto',
        ]);

        $user = Auth::user();

        $existing = WithdrawalAccount::where('email', $user->email)->where('method', $request->method)->first();
        if (!$existing) {
            return back()->with('error', 'No existing account to verify — set one up first.');
        }

        try {
            $sms->sendOtp($user->phone, 'withdrawal_account_update');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'A verification code has been sent to your phone.');
    }

    public function update(Request $request, SmsService $sms)
    {
        $request->validate([
            'method'         => 'required|in:mpesa,crypto',
            'name'           => 'required_if:method,mpesa|nullable|string|max:100',
            'phone'          => 'required_if:method,mpesa|nullable|string|max:20',
            'crypto_address' => 'required_if:method,crypto|nullable|string|max:64',
        ]);

        $user = Auth::user();

        $existing = WithdrawalAccount::where('email', $user->email)->where('method', $request->method)->first();

        if ($existing) {
            $request->validate(['code' => 'required|string']);

            if (!$sms->verifyOtp($user->phone, 'withdrawal_account_update', $request->code)) {
                return back()->with('error', 'Invalid or expired verification code.');
            }
        }

        WithdrawalAccount::updateOrCreate(
            ['email' => $user->email, 'method' => $request->method],
            $request->method === 'mpesa'
                ? ['name' => $request->name, 'phone' => $request->phone]
                : ['crypto_address' => $request->crypto_address]
        );

        return back()->with('success', 'Withdrawal account saved successfully.');
    }
}
