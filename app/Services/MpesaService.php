<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortcode;
    protected $passkey;
    protected $env;

    public function __construct()
    {
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->passkey = config('services.mpesa.passkey');
        $this->env = config('services.mpesa.environment', 'sandbox');
    }

    protected function getAccessToken(): string
    {
        $url = $this->env === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get($url);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get M-Pesa access token');
    }

    public function stkPush(string $phone, int $amount, string $reference, string $description = 'Wallet Deposit'): array
    {
        $accessToken = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $url = $this->env === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $phone = $this->formatPhoneNumber($phone);

        $response = Http::withToken($accessToken)
            ->post($url, [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $phone,
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $phone,
                'CallBackURL' => config('app.url') . '/api/mpesa/callback',
                'AccountReference' => $reference,
                'TransactionDesc' => $description,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('M-Pesa STK Push failed: ' . $response->body());
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }

        if (substr($phone, 0, 3) === '254') {
            return $phone;
        }

        if (substr($phone, 0, 4) === '+254') {
            return substr($phone, 1);
        }

        return '254' . $phone;
    }

    public function handleCallback(array $data): void
    {
        Log::info('M-Pesa Callback Received', ['data' => $data]);

        $body = $data['Body'] ?? [];
        $stkCallback = $body['stkCallback'] ?? [];
        $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $resultCode = $stkCallback['ResultCode'] ?? null;
        $resultDesc = $stkCallback['ResultDesc'] ?? null;

        if ($resultCode == 0) {
            $callbackMetadata = $stkCallback['CallbackMetadata'] ?? [];
            $items = $callbackMetadata['Item'] ?? [];

            $amount = null;
            $mpesaReceipt = null;
            $transactionDate = null;
            $phone = null;

            foreach ($items as $item) {
                if ($item['Name'] === 'Amount') {
                    $amount = $item['Value'] ?? null;
                }
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    $mpesaReceipt = $item['Value'] ?? null;
                }
                if ($item['Name'] === 'TransactionDate') {
                    $transactionDate = $item['Value'] ?? null;
                }
                if ($item['Name'] === 'PhoneNumber') {
                    $phone = $item['Value'] ?? null;
                }
            }

            $transaction = \App\Models\Transaction::where('transaction_id', $checkoutRequestId)
                ->orWhere('transaction_id', $merchantRequestId)
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'completed',
                    'mpesa_receipt' => $mpesaReceipt,
                    'description' => 'M-Pesa deposit - ' . $mpesaReceipt,
                ]);

                $user = $transaction->user;
                $user->balance += $transaction->amount;
                $user->save();
            }
        } else {
            $transaction = \App\Models\Transaction::where('transaction_id', $checkoutRequestId)
                ->orWhere('transaction_id', $merchantRequestId)
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'failed',
                    'description' => 'M-Pesa payment failed: ' . $resultDesc,
                ]);
            }
        }
    }
}
