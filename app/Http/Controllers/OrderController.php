<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'target_url' => 'required|url',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $service = Service::findOrFail($request->service_id);

        if ($request->quantity < $service->min_quantity) {
            return response()->json([
                'error' => 'Minimum quantity is ' . $service->min_quantity
            ], 422);
        }

        if ($request->quantity > $service->max_quantity) {
            return response()->json([
                'error' => 'Maximum quantity is ' . $service->max_quantity
            ], 422);
        }

        $totalPrice = $service->price * $request->quantity;

        if ($user->balance < $totalPrice) {
            return response()->json([
                'error' => 'Insufficient balance. You need KES ' . number_format($totalPrice, 2)
            ], 422);
        }

        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'order_number' => 'ORD' . time() . rand(1000, 9999),
            'target_url' => $request->target_url,
            'quantity' => $request->quantity,
            'price' => $totalPrice,
            'status' => 'pending'
        ]);

        $user->balance -= $totalPrice;
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'transaction_id' => 'TXN' . time() . rand(1000, 9999),
            'type' => 'purchase',
            'payment_method' => null,
            'amount' => $totalPrice,
            'balance_before' => $user->balance + $totalPrice,
            'balance_after' => $user->balance,
            'status' => 'completed',
            'description' => 'Order #' . $order->order_number
        ]);

        $service->increment('order_count');

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'data' => $order
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $orders = $user->orders()->with('service')
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }

    public function show($id): JsonResponse
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->with('service')
            ->findOrFail($id);

        return response()->json($order);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $orders = Order::with(['user', 'service'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('order_number', 'LIKE', "%{$search}%")
                    ->orWhere('target_url', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->paginate(50);

        return response()->json($orders);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,completed,cancelled,refunded',
            'delivered' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = Order::findOrFail($id);
        $order->status = $request->status;

        if ($request->has('delivered')) {
            $order->delivered = $request->delivered;
            $order->remaining = $order->quantity - $request->delivered;
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }

    public function stats(): JsonResponse
    {
        $user = Auth::user();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
            'total_spent' => $user->orders()->sum('price')
        ];

        return response()->json($stats);
    }
}
