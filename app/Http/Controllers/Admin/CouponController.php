<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderByDesc('id')->paginate(20)->onEachSide(1);
        return Inertia::render('Admin/Coupons', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'     => 'nullable|string|max:30|unique:coupons,code',
            'amount'   => 'required|numeric|min:1',
            'minutes'  => 'required|integer|min:1',
            'max_uses' => 'nullable|integer|min:0',
        ]);

        $code = $request->filled('code')
            ? strtoupper(trim($request->code))
            : strtoupper(Str::random(8));

        Coupon::create([
            'code'       => $code,
            'amount'     => $request->amount,
            'expires_at' => now()->addMinutes((int) $request->minutes),
            'max_uses'   => (int) ($request->max_uses ?? 0),
            'used_count' => 0,
        ]);

        return back()->with('success', 'Coupon "' . $code . '" created successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->uses()->delete();
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }
}
