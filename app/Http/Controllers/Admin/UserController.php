<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = \App\Models\User::orderByDesc('ID');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('ID', $search);
            });
        }

        $users = $query->paginate(20)->onEachSide(1)->withQueryString();

        // Resolve upline emails in one query to avoid N+1
        $uplineIds = $users->pluck('refer')->filter()->unique()->values();
        $uplineEmails = \App\Models\User::whereIn('ID', $uplineIds)
            ->pluck('email', 'ID');

        return Inertia::render('Admin/Users', compact('users', 'uplineEmails', 'search'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Active,Inactive']);
        \App\Models\User::findOrFail($id)->update(['status' => $request->status]);
        return back()->with('success', 'User status updated.');
    }

    public function makeAdmin(Request $request, $id)
    {
        \App\Models\User::findOrFail($id)->update(['role' => 'admin']);
        return back()->with('success', 'User promoted to admin.');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = \App\Models\User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', "Password reset for {$user->email}.");
    }
}
