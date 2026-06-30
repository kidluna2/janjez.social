<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function __construct(private MpesaService $mpesa) {}

    public function deposit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10',
            'phone' => 'required|string',
            'payment_method' => 'required|in:mpesa,bank,card,crypto'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $amount = (int) $request->amount;
        $reference = 'DEP' . time() . rand(1000, 9999);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'transaction_id' => $reference,
            'type' => 'deposit',
            'payment_method' => $request->payment_method,
            'amount' => $amount,
            'balance_before' => $user->balance,
            'balance_after' => $user->balance + $amount,
            'status' => 'pending',
            'description' => 'Wallet deposit via ' . strtoupper($request->payment_method)
        ]);

        if ($request->payment_method === 'mpesa') {
            try {
                $response = $this->mpesa->stkPush(
                    $request->phone,
                    $amount,
                    $reference,
                    'Deposit to SMM Wallet'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'M-Pesa STK Push sent successfully',
                    'data' => [
                        'transaction' => $transaction,
                        'mpesa_response' => $response
                    ]
                ]);
            } catch (\Exception $e) {
                $transaction->update(['status' => 'failed']);
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Deposit initiated successfully',
            'data' => $transaction
        ]);
    }

    public function callback(Request $request): JsonResponse
    {
        $this->mpesa->handleCallback($request->all());
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    public function transactions(): JsonResponse
    {
        $user = Auth::user();
        $transactions = $user->transactions()->latest()->paginate(20);
        return response()->json($transactions);
    }
}
