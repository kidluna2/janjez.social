<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ResellerController extends Controller
{
    public function services(Request $request): JsonResponse
    {
        $services = Service::with('category')
            ->where('status', 'active')
            ->paginate(50);

        return response()->json($services);
    }

    public function placeOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'link' => 'required|url',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $service = Service::findOrFail($request->service_id);

        if ($request->quantity < $service->min_quantity || $request->quantity > $service->max_quantity) {
            return response()->json([
                'error' => 'Quantity must be between ' . $service->min_quantity . ' and ' . $service->max_quantity
            ], 422);
        }

        $totalPrice = $service->price * $request->quantity;

        if ($user->balance < $totalPrice) {
            return response()->json([
                'error' => 'Insufficient balance. Required: KES ' . number_format($totalPrice, 2)
            ], 422);
        }

        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'order_number' => 'ORD' . time() . rand(1000, 9999),
            'target_url' => $request->link,
            'quantity' => $request->quantity,
            'price' => $totalPrice,
            'status' => 'pending'
        ]);

        $user->balance -= $totalPrice;
        $user->save();

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    public function status($id): JsonResponse
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->findOrFail($id);

        return response()->json([
            'order' => $order->order_number,
            'status' => $order->status,
            'quantity' => $order->quantity,
            'delivered' => $order->delivered,
            'remaining' => $order->remaining
        ]);
    }

    public function balance(): JsonResponse
    {
        return response()->json([
            'balance' => Auth::user()->balance,
            'currency' => 'KES'
        ]);
    }
}
