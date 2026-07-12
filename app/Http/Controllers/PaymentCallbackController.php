<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    private const FAILED_STATUSES = ['FAILED', 'CANCELLED', 'EXPIRED'];

    public function handle(Request $request)
    {
        $data = $request->json()->all();

        Log::info('Palpluss STK Callback received', $data);

        // Only process terminal transaction events
        $event = $data['event'] ?? null;
        if ($event !== 'transaction.updated') {
            return response()->json(['status' => 'ignored'], 200);
        }

        $tx = $data['transaction'] ?? null;
        if (!$tx) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $palplussId  = $tx['id']                  ?? null;
        $trackingId  = $tx['external_reference']   ?? null;
        $status      = strtoupper($tx['status']    ?? '');
        $amount      = (float) ($tx['amount']      ?? 0);
        $mpesaRef    = $tx['mpesa_receipt']         ?? null;
        $resultCode  = $tx['result_code']           ?? null;
        $resultDesc  = $tx['result_desc']           ?? null;
        $txType      = strtoupper($tx['type']       ?? '');

        // Must be an STK transaction
        if ($txType !== 'STK') {
            return response()->json(['status' => 'ignored'], 200);
        }

        if (!$trackingId) {
            Log::warning('STK Callback: missing external_reference', $tx);
            return response()->json(['status' => 'ignored'], 200);
        }

        $transaction = Transaction::where('tracking_id', $trackingId)->first();

        if (!$transaction) {
            Log::warning('STK Callback: transaction not found', ['tracking_id' => $trackingId]);
            return response()->json(['status' => 'not_found'], 200);
        }

        // Idempotency — skip if already resolved
        if ($transaction->status !== 'Pending') {
            Log::info('STK Callback: already processed', [
                'tracking_id' => $trackingId,
                'status'      => $transaction->status,
            ]);
            return response()->json(['status' => 'already_processed'], 200);
        }

        // ── Failed / Cancelled / Expired ─────────────────────────────────
        if (in_array($status, self::FAILED_STATUSES)) {
            $transaction->status  = 'Failed';
            $transaction->details = $resultDesc ?? $status;
            $transaction->save();

            Log::warning('STK Callback: payment failed', [
                'tracking_id' => $trackingId,
                'palpluss_id' => $palplussId,
                'status'      => $status,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc,
                'email'       => $transaction->email,
            ]);

            return response()->json(['status' => 'failed_recorded'], 200);
        }

        // ── Unrecognised / non-success ────────────────────────────────────
        if ($status !== 'SUCCESS' || $amount <= 0) {
            Log::warning('STK Callback: unrecognised status', [
                'tracking_id' => $trackingId,
                'status'      => $status,
            ]);
            return response()->json(['status' => 'unrecognised'], 200);
        }

        // ── Success ───────────────────────────────────────────────────────
        DB::transaction(function () use ($transaction, $amount, $mpesaRef, $palplussId, $trackingId) {
            $email = $transaction->email;

            $transaction->status    = 'Success';
            $transaction->details   = $mpesaRef ?? '';
            $transaction->RecAmount = $amount;
            $transaction->save();

            $earnings = Earning::where('email', $email)->first();
            if ($earnings) {
                $earnings->balance += $amount;
                $earnings->deposit += $amount;
                $earnings->save();
            } else {
                Log::warning('STK Callback: earnings not found', ['email' => $email]);
            }

            User::where('email', $email)->update(['status' => 'Active']);

            // 10% referral commission to sponsor
            $user = User::where('email', $email)->first();
            if ($user && $user->refer) {
                $sponsor = User::find($user->refer);
                if ($sponsor && $sponsor->earnings) {
                    $commission = $amount * 0.10;
                    $sponsor->earnings->balance  += $commission;
                    $sponsor->earnings->referral += $commission;
                    $sponsor->earnings->save();

                    Transaction::create([
                        'email'     => $sponsor->email,
                        'phone'     => $sponsor->phone,
                        'amount'    => $commission,
                        'type'      => 'Referral',
                        'status'    => 'Success',
                        'details'   => 'Commission from ' . $email,
                        'RecAmount' => $commission,
                    ]);
                }
            }
        });

        Log::info('STK Callback: deposit processed', [
            'tracking_id' => $trackingId,
            'palpluss_id' => $palplussId,
            'amount'      => $amount,
            'mpesa_ref'   => $mpesaRef,
            'email'       => $transaction->email,
        ]);

        return response()->json(['status' => 'processed'], 200);
    }
}
