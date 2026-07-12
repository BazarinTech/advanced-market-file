<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PalplussService
{
    private string $baseUrl;
    private string $auth;
    private string $callbackUrl;
    private string $channelId;

    public function __construct()
    {
        $this->baseUrl     = config('services.palpluss.base_url');
        $this->auth        = config('services.palpluss.auth');
        $this->callbackUrl = config('services.palpluss.callback_url');
        $this->channelId   = config('services.palpluss.channel_id');
    }

    private function headers(): array
    {
        return [
            'Authorization' => 'Basic ' . $this->auth,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    public function initiatePayout(float $amount, string $phone, string $reference): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/b2c/payouts', [
                'amount'      => $amount,
                'phone'       => $phone,
                'reference'   => $reference,
                'description' => 'Withdrawal - ' . $reference,
                'callbackUrl' => config('app.url') . '/callback/b2c',
            ]);

        $data = $response->json();

        if ($response->successful()) {
            return ['status' => 'Success', 'data' => $data];
        }

        return [
            'status'  => 'Failed',
            'message' => $data['message'] ?? 'Payout failed. Please try again.',
            'data'    => $data,
        ];
    }

    public function initiateDeposit(float $amount, string $phone, string $trackingId): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/payments/stk', [
                'amount'          => $amount,
                'phone'           => $phone,
                'channelId'       => $this->channelId,
                'accountReference'=> $trackingId,
                'transactionDesc' => 'Deposit - ' . $trackingId,
                'callbackUrl'     => $this->callbackUrl,
            ]);

        $data = $response->json();

        if ($response->successful()) {
            return [
                'status'  => 'Success',
                'message' => 'M-Pesa STK push initiated. Check your phone and enter your M-Pesa PIN.',
                'data'    => $data,
            ];
        }

        return [
            'status'  => 'Failed',
            'message' => $data['message'] ?? 'STK push failed. Please try again or contact support.',
            'data'    => $data,
        ];
    }
}
