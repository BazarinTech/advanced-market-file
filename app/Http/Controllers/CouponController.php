<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUse;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function show()
    {
        return view('dashboard.coupon');
    }

    public function redeem(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $user   = Auth::user();
        $code   = strtoupper(trim($request->code));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        if ($coupon->isExpired()) {
            return back()->with('error', 'This coupon has expired.');
        }

        if ($coupon->isExhausted()) {
            return back()->with('error', 'This coupon has already been fully redeemed.');
        }

        if ($coupon->alreadyUsedBy($user->email)) {
            return back()->with('error', 'You have already redeemed this coupon.');
        }

        // Credit balance
        $earnings = $user->earnings;
        $earnings->balance += $coupon->amount;
        $earnings->save();

        // Record use
        CouponUse::create([
            'coupon_id'  => $coupon->id,
            'user_email' => $user->email,
            'used_at'    => now(),
        ]);

        $coupon->increment('used_count');

        // Log as transaction
        Transaction::create([
            'email'     => $user->email,
            'phone'     => $user->phone,
            'amount'    => $coupon->amount,
            'RecAmount' => $coupon->amount,
            'type'      => 'Deposit',
            'status'    => 'Success',
            'details'   => 'Coupon: ' . $coupon->code,
        ]);

        return back()->with('success', 'Coupon redeemed! Kes ' . number_format($coupon->amount, 2) . ' added to your balance.');
    }
}
