<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function home()
    {
        $user     = Auth::user();
        $earnings = $user->earnings;
        $downline = User::where('refer', $user->ID)->get();

        // Portfolio chart data: last 7 deposit points + current balance
        $deposits = Transaction::where('email', $user->email)
            ->whereIn('type', ['Deposit', 'Deposits'])->where('status', 'Success')
            ->orderBy('date')->get(['RecAmount', 'date']);

        $chartLabels  = [];
        $chartValues  = [];
        $running      = 0;
        foreach ($deposits as $d) {
            $running       += $d->RecAmount;
            $chartLabels[]  = \Carbon\Carbon::parse($d->date)->format('d M');
            $chartValues[]  = $running;
        }
        // Always include today's balance as last point
        $chartLabels[] = 'Now';
        $chartValues[] = (float) $earnings->balance;

        // Keep max 10 points
        if (count($chartLabels) > 10) {
            $chartLabels = array_slice($chartLabels, -10);
            $chartValues = array_slice($chartValues, -10);
        }

        return Inertia::render('Dashboard/Home', [
            'downline'     => $downline->count(),
            'numActive'    => $downline->where('status', 'Active')->count(),
            'chartLabels'  => $chartLabels,
            'chartValues'  => $chartValues,
        ]);
    }

    public function packages()
    {
        $packages = Package::where('active', true)->orderBy('amount')->get();

        return Inertia::render('Dashboard/Packages', [
            'packages' => $packages,
        ]);
    }

    public function buyPackage(Request $request)
    {
        $request->validate(['package' => 'required|integer|exists:packages,id']);

        $user     = Auth::user();
        $earnings = $user->earnings;
        $pkg      = Package::where('id', $request->package)->where('active', true)->firstOrFail();

        if ($earnings->balance < $pkg->amount) {
            return back()->with('error', 'Insufficient balance. Please top up your account.');
        }

        Order::create([
            'email'           => $user->email,
            'package'         => $pkg->name,
            'amount'          => $pkg->amount,
            'daily'           => $pkg->daily,
            'cycle'           => $pkg->days,
            'totals'          => $pkg->daily * $pkg->days,
            'status'          => 'Active',
            'last_claimed_at' => null,
        ]);

        $earnings->balance -= $pkg->amount;
        $earnings->save();

        $user->status = 'Active';
        $user->save();

        return redirect()->route('task')->with('success', 'Plan activated successfully!');
    }

    public function account()
    {
        $user     = Auth::user();
        $downline = User::where('refer', $user->ID)->get();

        return Inertia::render('Dashboard/Account', [
            'downline'              => $downline->count(),
            'numActive'             => $downline->where('status', 'Active')->count(),
            'link_whatsapp'         => Setting::get('link_whatsapp'),
            'link_telegram'         => Setting::get('link_telegram'),
            'link_customer_support' => Setting::get('link_customer_support'),
            'link_download_app'     => Setting::get('link_download_app'),
        ]);
    }

    public function userSettings()
    {
        return Inertia::render('Dashboard/User');
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        if ($request->filled('newPass')) {
            $request->validate([
                'prevPass' => 'required',
                'newPass'  => 'required|min:8|same:conPass',
                'conPass'  => 'required',
            ]);

            $legacyMatch = $user->passwrd === $request->prevPass;
            $hashedMatch = $user->password && Hash::check($request->prevPass, $user->password);

            if (!$legacyMatch && !$hashedMatch) {
                return back()->with('error', 'Incorrect current password.');
            }

            $user->passwrd  = $request->newPass;
            $user->password = Hash::make($request->newPass);
            $user->save();

            return back()->with('success', 'Password updated successfully.');
        }

        $request->validate(['phone' => 'required|string|max:20']);
        $user->phone = $request->phone;
        $user->save();

        return back()->with('success', 'Phone updated successfully.');
    }

    public function packagesTable()
    {
        $packages = Package::orderBy('amount')->get();
        return Inertia::render('Dashboard/Table', compact('packages'));
    }
}
