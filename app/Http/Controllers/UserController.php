<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'country' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only('name', 'phone', 'country'));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    public function balance(Request $request): JsonResponse
    {
        return response()->json([
            'balance' => $request->user()->balance
        ]);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $users = User::when($request->search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        })
            ->latest()
            ->paginate(50);

        return response()->json($users);
    }

    public function updateBalance(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'balance' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($id);
        $oldBalance = $user->balance;
        $user->balance = $request->balance;
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'transaction_id' => 'TXN' . time() . rand(1000, 9999),
            'type' => 'adjustment',
            'payment_method' => 'manual',
            'amount' => $request->balance - $oldBalance,
            'balance_before' => $oldBalance,
            'balance_after' => $request->balance,
            'status' => 'completed',
            'description' => 'Balance adjustment by admin'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balance updated successfully',
            'data' => $user
        ]);
    }

    public function updateRole(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:admin,reseller,user',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $user
        ]);
    }
}
