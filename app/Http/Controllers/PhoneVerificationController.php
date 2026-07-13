<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PhoneVerificationController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if ($user->phone_verified_at) {
            return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'home');
        }

        return Inertia::render('Auth/VerifyPhone', [
            'phone' => $user->phone,
        ]);
    }

    public function verify(Request $request, SmsService $sms)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();

        if (! $sms->verifyOtp($user->phone, 'registration', $request->code)) {
            return back()->with('error', 'Invalid or expired code. Please try again.');
        }

        $user->phone_verified_at = now();
        $user->save();

        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'home')
            ->with('success', 'Phone verified successfully!');
    }

    public function resend(SmsService $sms)
    {
        $user = Auth::user();

        try {
            $sms->sendOtp($user->phone, 'registration');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'A new code has been sent to your phone.');
    }
}
