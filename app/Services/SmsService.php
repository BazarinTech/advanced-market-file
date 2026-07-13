<?php

namespace App\Services;

use App\Models\VerificationCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SmsService
{
    /**
     * Normalize a Kenyan phone number to the 2547XXXXXXXX format the
     * gateway expects, regardless of how the user typed it in
     * (07XXXXXXXX, +2547XXXXXXXX, 2547XXXXXXXX, 7XXXXXXXX).
     */
    public function sanitizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '254')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '254' . substr($digits, 1);
        }

        if (strlen($digits) === 9) {
            return '254' . $digits;
        }

        return $digits;
    }

    /**
     * Whether the given phone number falls in a known Safaricom number
     * range. Only Safaricom numbers are accepted for registration since
     * SMS delivery and M-Pesa STK push both depend on it.
     */
    public function isSafaricomNumber(string $phone): bool
    {
        $sanitized = $this->sanitizePhone($phone);

        if (! str_starts_with($sanitized, '254') || strlen($sanitized) !== 12) {
            return false;
        }

        $local = substr($sanitized, 3);

        return (bool) preg_match('/^(7[0124]\d{7}|11[0-5]\d{6})$/', $local);
    }

    protected function generateCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    /**
     * Send a 6-digit verification code to the given phone via HostPinnacle,
     * and cache it server-side (in the verification_codes table) so it can
     * be checked later by verifyOtp(). Throws on any delivery failure.
     */
    public function sendOtp(string $phone, string $type): void
    {
        $mobile = $this->sanitizePhone($phone);
        $code   = $this->generateCode();
        $message = "Your verification code is: {$code}. It is valid for 5 minutes.";

        $response = Http::asForm()
            ->withHeaders([
                'apikey'        => config('services.hostpinnacle.api_key'),
                'cache-control' => 'no-cache',
            ])
            ->timeout(30)
            ->post('https://smsportal.hostpinnacle.co.ke/SMSApi/send', [
                'userid'         => config('services.hostpinnacle.userid'),
                'password'       => config('services.hostpinnacle.password'),
                'mobile'         => $mobile,
                'msg'            => $message,
                'senderid'       => config('services.hostpinnacle.sender_id'),
                'msgType'        => 'text',
                'duplicatecheck' => 'true',
                'output'         => 'json',
                'sendMethod'     => 'quick',
            ]);

        $decoded = $response->json();

        if (! is_array($decoded)) {
            Log::error('HostPinnacle SMS: non-JSON response', ['body' => $response->body()]);
            throw new RuntimeException('Could not send the verification code right now. Please try again.');
        }

        $status     = strtolower($decoded['status'] ?? '');
        $statusCode = (string) ($decoded['statusCode'] ?? '');
        $invalid    = trim((string) ($decoded['invalidMobile'] ?? ''));

        $isSuccess = $status === 'success' && $statusCode === '200' && $invalid === '';

        if (! $isSuccess) {
            Log::error('HostPinnacle SMS failed', ['response' => $decoded]);
            throw new RuntimeException('Could not send the verification code: ' . ($decoded['reason'] ?? 'unknown gateway error'));
        }

        VerificationCode::create([
            'phone'      => $mobile,
            'code'       => $code,
            'type'       => $type,
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    /**
     * Check a submitted code against the most recent unexpired, unused code
     * sent to this phone for this purpose. Consumes the code on success so
     * it can't be reused.
     */
    public function verifyOtp(string $phone, string $type, string $code): bool
    {
        $mobile = $this->sanitizePhone($phone);

        $record = VerificationCode::where('phone', $mobile)
            ->where('type', $type)
            ->where('code', $code)
            ->whereNull('consumed_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (! $record) {
            return false;
        }

        $record->consumed_at = now();
        $record->save();

        return true;
    }
}
