<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUse;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CouponController extends Controller
{
    public function show()
    {
        return Inertia::render('Dashboard/Coupon');
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

        $rate = (float) Setting::get('usd_kes_rate', 130);

        return back()->with('success', 'Coupon redeemed! $' . number_format($coupon->amount / $rate, 2) . ' added to your balance.');
    }
}
