<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function orders(Request $request): JsonResponse
    {
        $days = $request->days ?? 30;
        $startDate = now()->subDays($days);

        $orders = Order::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_orders, SUM(price) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function revenue(Request $request): JsonResponse
    {
        $days = $request->days ?? 30;
        $startDate = now()->subDays($days);

        $revenue = Transaction::where('type', 'purchase')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total_revenue')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($revenue);
    }

    public function users(Request $request): JsonResponse
    {
        $days = $request->days ?? 30;
        $startDate = now()->subDays($days);

        $users = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as new_users')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($users);
    }
}
