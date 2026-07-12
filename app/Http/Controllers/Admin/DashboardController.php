<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $earnings   = \App\Models\Earning::all();
        $totalDeps  = $earnings->sum('deposit');
        $totalWith  = $earnings->sum('withdraw');
        $totalBals  = $earnings->sum('balance');

        $totalUsers = \App\Models\User::count();
        $inactive   = \App\Models\User::where('status', 'Inactive')->count();
        $active     = $totalUsers - $inactive;

        $joinedToday = \App\Models\User::whereDate('date', today())->count();

        $usersDeposited = \App\Models\Transaction::where('type', 'Deposit')
            ->where('status', 'Success')
            ->distinct('email')
            ->count('email');

        return Inertia::render('Admin/Dashboard', compact(
            'totalDeps', 'totalWith', 'totalBals',
            'totalUsers', 'active', 'inactive', 'joinedToday',
            'usersDeposited'
        ));
    }
}
