<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use App\Models\PasswordRecoveryRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid account.'])->withInput();
        }

        // Try hashed password first, then fall back to legacy plaintext passwrd
        $authenticated = false;

        if ($user->password && Hash::check($request->password, $user->password)) {
            $authenticated = true;
        } elseif ($user->passwrd === $request->password) {
            // Migrate plaintext password to hashed on first login
            $user->password = Hash::make($request->password);
            $user->save();
            $authenticated = true;
        }

        if (!$authenticated) {
            return back()->withErrors(['email' => 'Invalid account.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    }

    public function showRegister(Request $request)
    {
        $ref = $request->query('invite', '1631');
        return Inertia::render('Auth/Register', compact('ref'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20',
            'country'  => 'required|string',
            'ref'      => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'email'    => $request->email,
            'phone'    => $request->phone,
            'passwrd'  => $request->password,
            'password' => Hash::make($request->password),
            'refer'    => $request->ref,
            'country'  => $request->country,
            'status'   => 'Inactive',
            'role'     => 'user',
        ]);

        Earning::create(['email' => $user->email]);

        return redirect()->route('login')->with('success', 'Account created! Please login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showForgot()
    {
        return Inertia::render('Auth/Forgot', [
            'support_email'   => config('services.support.email'),
            'support_phone'   => config('services.support.phone'),
            'support_network' => config('services.support.network'),
            'support_url'     => config('services.links.customer_support'),
        ]);
    }

    public function submitForgot(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'phone'   => 'required|string|max:20',
            'network' => 'required|string',
        ]);

        PasswordRecoveryRequest::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'network' => $request->network,
            'status'  => 'Pending',
        ]);

        return back()->with('success', 'Request received. Our support team will contact you shortly.');
    }
}
