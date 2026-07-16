<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
            'login'    => 'required|string',
            'password' => 'required|min:8',
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('phone', $request->login)
            ->first();

        if (!$user) {
            return back()->withErrors(['login' => 'Invalid account.'])->withInput();
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
            return back()->withErrors(['login' => 'Invalid account.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        if (! $user->phone_verified_at) {
            return redirect()->route('verify-phone');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
        }

        return redirect()->route('home')->with('success', 'Welcome back!');
    }

    public function showRegister(Request $request)
    {
        $ref      = $request->query('invite');
        $refValid = $ref ? User::where('invite_code', $ref)->exists() : false;

        return Inertia::render('Auth/Register', [
            // Note: not named "ref" — Vue treats a prop literally named `ref` as the
            // special template-ref vnode property and silently never delivers it to the
            // component's props, regardless of how it's bound (v-bind spread included).
            'refCode'  => $refValid ? $ref : null,
            'refValid' => $refValid,
        ]);
    }

    public function register(Request $request, SmsService $sms)
    {
        $request->validate([
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|size:10|unique:users,phone',
            'country'  => 'required|string',
            'ref'      => ['required', 'string', 'size:6', Rule::exists('users', 'invite_code')],
            'password' => 'required|min:8|confirmed',
        ], [
            'phone.size' => 'Phone number must be 10 digits (e.g. 0712345678).',
            'ref.exists' => 'Invalid referral code.',
        ]);

        $sponsor = User::where('invite_code', $request->ref)->first();

        $user = User::create([
            'email'       => $request->email,
            'phone'       => $request->phone,
            'passwrd'     => $request->password,
            'password'    => Hash::make($request->password),
            'refer'       => $sponsor->ID,
            'invite_code' => User::generateInviteCode(),
            'country'     => $request->country,
            'status'      => 'Inactive',
            'role'        => 'user',
        ]);

        Earning::create(['email' => $user->email]);

        Auth::login($user);

        try {
            $sms->sendOtp($user->phone, 'registration');
        } catch (\Throwable $e) {
            // Non-fatal — the verify-phone page offers a "resend code" action.
        }

        return redirect()->route('verify-phone')->with('success', 'Account created! Enter the code sent to your phone.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Signed out successfully.');
    }
}
