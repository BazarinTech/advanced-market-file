<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $level1 = User::where('refer', $user->ID)->orderByDesc('ID')->get();
        $level2 = $level1->isEmpty()
            ? collect()
            : User::whereIn('refer', $level1->pluck('ID'))->orderByDesc('ID')->get();
        $level3 = $level2->isEmpty()
            ? collect()
            : User::whereIn('refer', $level2->pluck('ID'))->orderByDesc('ID')->get();

        $allDownline     = $level1->concat($level2)->concat($level3);
        $downlineEmails  = $allDownline->pluck('email');

        // Total successful deposits per downline member (single query, all levels)
        $depositTotals = Transaction::whereIn('email', $downlineEmails)
            ->where('type', 'Deposit')
            ->where('status', 'Success')
            ->selectRaw('email, SUM(RecAmount) as total')
            ->groupBy('email')
            ->pluck('total', 'email');

        $numActive    = $allDownline->where('status', 'Active')->count();
        $numDeposited = $depositTotals->count();

        return Inertia::render('Dashboard/Team', [
            'downline' => [
                'level1' => $level1->values(),
                'level2' => $level2->values(),
                'level3' => $level3->values(),
            ],
            'numActive'     => $numActive,
            'numDeposited'  => $numDeposited,
            'depositTotals' => $depositTotals,
        ]);
    }
}
