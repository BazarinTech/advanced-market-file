<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    public function showForgot()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'link_customer_support' => Setting::get('link_customer_support'),
        ]);
    }

    public function sendCode(Request $request, SmsService $sms)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (! $user) {
            return back()->withErrors(['phone' => 'No account found with that phone number.'])->withInput();
        }

        try {
            $sms->sendOtp($user->phone, 'password_reset');
        } catch (\Throwable $e) {
            return back()->withErrors(['phone' => $e->getMessage()])->withInput();
        }

        return redirect()->route('password.reset', ['phone' => $user->phone])
            ->with('success', 'A verification code has been sent to your phone.');
    }

    public function showReset(Request $request)
    {
        return Inertia::render('Auth/ResetPassword', [
            'phone' => $request->query('phone', ''),
        ]);
    }

    public function reset(Request $request, SmsService $sms)
    {
        $request->validate([
            'phone'    => 'required|string',
            'code'     => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (! $user) {
            return back()->withErrors(['phone' => 'No account found with that phone number.'])->withInput();
        }

        if (! $sms->verifyOtp($request->phone, 'password_reset', $request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired code.'])->withInput();
        }

        $user->passwrd  = $request->password;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Password reset successfully! Please log in.');
    }
}
