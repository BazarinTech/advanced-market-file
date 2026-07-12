<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $transactions = \App\Models\Transaction::where('email', $user->email)
            ->orderByDesc('ID')
            ->get();

        return view('dashboard.transaction', [
            'user'         => $user,
            'transactions' => $transactions,
        ]);
    }
}
