<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $transactions = \App\Models\Transaction::where('email', $user->email)
            ->orderByDesc('ID')
            ->get();

        return Inertia::render('Dashboard/Transaction', [
            'transactions' => $transactions,
        ]);
    }
}
