<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $earnings = $user->earnings;

        $downline = User::where('refer', $user->ID)
            ->orderByDesc('ID')
            ->get();

        $downlineEmails = $downline->pluck('email');

        // Total successful deposits per downline member (single query)
        $depositTotals = Transaction::whereIn('email', $downlineEmails)
            ->where('type', 'Deposit')
            ->where('status', 'Success')
            ->selectRaw('email, SUM(RecAmount) as total')
            ->groupBy('email')
            ->pluck('total', 'email');

        $numActive    = $downline->where('status', 'Active')->count();
        $numDeposited = $depositTotals->count();

        return view('dashboard.team', [
            'user'          => $user,
            'earnings'      => $earnings,
            'downline'      => $downline,
            'numActive'     => $numActive,
            'numDeposited'  => $numDeposited,
            'depositTotals' => $depositTotals,
        ]);
    }
}
