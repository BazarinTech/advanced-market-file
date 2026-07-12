<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayoutCallbackController extends Controller
{
    private const FAILED_STATUSES = ['FAILED', 'CANCELLED', 'EXPIRED'];

    public function handle(Request $request)
    {
        $data = $request->json()->all();

        Log::info('Palpluss B2C Callback received', $data);

        // Only process terminal transaction events
        $event = $data['event'] ?? null;
        if ($event !== 'transaction.updated') {
            return response()->json(['status' => 'ignored'], 200);
        }

        $tx = $data['transaction'] ?? null;
        if (!$tx) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $palplussId = $tx['id']                  ?? null;
        $reference  = $tx['external_reference']   ?? null;
        $status     = strtoupper($tx['status']    ?? '');
        $amount     = (float) ($tx['amount']      ?? 0);
        $mpesaRef   = $tx['mpesa_receipt']         ?? null;
        $resultCode = $tx['result_code']           ?? null;
        $resultDesc = $tx['result_desc']           ?? null;
        $txType     = strtoupper($tx['type']       ?? '');

        // Must be a B2C transaction
        if ($txType !== 'B2C') {
            return response()->json(['status' => 'ignored'], 200);
        }

        if (!$reference) {
            Log::warning('B2C Callback: missing external_reference', $tx);
            return response()->json(['status' => 'ignored'], 200);
        }

        $transaction = Transaction::where('tracking_id', $reference)
            ->where('type', 'Withdraw')
            ->first();

        if (!$transaction) {
            Log::warning('B2C Callback: transaction not found', ['reference' => $reference]);
            return response()->json(['status' => 'not_found'], 200);
        }

        // Idempotency — only process if still Pending
        if ($transaction->status !== 'Pending') {
            Log::info('B2C Callback: already processed', [
                'reference' => $reference,
                'status'    => $transaction->status,
            ]);
            return response()->json(['status' => 'already_processed'], 200);
        }

        // ── Success ───────────────────────────────────────────────────────
        if ($status === 'SUCCESS') {
            $transaction->status  = 'Success';
            $transaction->details = $mpesaRef ?? $reference;
            $transaction->save();

            Log::info('B2C Callback: withdrawal successful', [
                'reference'   => $reference,
                'palpluss_id' => $palplussId,
                'amount'      => $amount,
                'mpesa_ref'   => $mpesaRef,
                'email'       => $transaction->email,
            ]);

            return response()->json(['status' => 'processed'], 200);
        }

        // ── Failed / Cancelled / Expired ─────────────────────────────────
        if (in_array($status, self::FAILED_STATUSES)) {
            DB::transaction(function () use ($transaction, $status, $resultCode, $resultDesc) {
                $refundAmount = (float) $transaction->amount;

                $transaction->status  = 'Failed';
                $transaction->details = $resultDesc ?? $status;
                $transaction->save();

                $earnings = Earning::where('email', $transaction->email)->first();
                if ($earnings && $refundAmount > 0) {
                    $earnings->balance  += $refundAmount;
                    $earnings->withdraw  = max(0, $earnings->withdraw - $refundAmount);
                    $earnings->save();

                    Log::info('B2C Callback: balance refunded', [
                        'email'         => $transaction->email,
                        'refund_amount' => $refundAmount,
                        'status'        => $status,
                        'result_code'   => $resultCode,
                        'result_desc'   => $resultDesc,
                    ]);
                } else {
                    Log::warning('B2C Callback: earnings not found for refund', [
                        'email' => $transaction->email,
                    ]);
                }
            });

            Log::warning('B2C Callback: withdrawal failed', [
                'reference'   => $reference,
                'palpluss_id' => $palplussId,
                'status'      => $status,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc,
                'email'       => $transaction->email,
            ]);

            return response()->json(['status' => 'failed_recorded'], 200);
        }

        // ── Unrecognised status ───────────────────────────────────────────
        Log::warning('B2C Callback: unrecognised status', [
            'reference' => $reference,
            'status'    => $status,
        ]);

        return response()->json(['status' => 'unrecognised'], 200);
    }
}
