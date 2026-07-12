<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use App\Models\Setting;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $orders = Order::where('email', $user->email)->orderByDesc('ID')->get();

        // Load package images keyed by name
        $packageImages = Package::pluck('image', 'name');

        // Any active order that can be claimed?
        $canClaim = $orders->where('status', 'Active')->contains(fn($o) => $o->canClaim());

        $claimImgFile = Setting::get('claim_image');
        $claimImgSrc  = $claimImgFile
            ? asset('images/' . $claimImgFile)
            : asset('images/orderL.jpeg');

        return view('dashboard.task', [
            'user'          => $user,
            'earnings'      => $user->earnings,
            'orders'        => $orders,
            'canClaim'      => $canClaim,
            'packageImages' => $packageImages,
            'claimImgSrc'   => $claimImgSrc,
        ]);
    }

    public function claim(Request $request)
    {
        $user     = auth()->user();
        $earnings = $user->earnings;

        $orders = Order::where('email', $user->email)
            ->where('status', 'Active')
            ->get()
            ->filter(fn($o) => $o->canClaim());

        if ($orders->isEmpty()) {
            return back()->with('error', 'No earnings to claim yet. Please wait 24 hours after purchase or last claim.');
        }

        $total = 0;
        foreach ($orders as $order) {
            $newEarnings = $order->earnings + $order->daily;
            $capped      = min($newEarnings, $order->totals);
            $credited    = $capped - $order->earnings;

            $order->earnings        = $capped;
            $order->status          = $capped >= $order->totals ? 'Inactive' : 'Active';
            $order->last_claimed_at = now();
            $order->save();

            $total += $credited;
        }

        $earnings->balance += $total;
        $earnings->totals  += $total;
        $earnings->save();

        return back()->with('success', 'Earnings claimed successfully! +Kes ' . number_format($total, 2));
    }
}
