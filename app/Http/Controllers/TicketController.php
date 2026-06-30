<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'sometimes|in:low,medium,high,urgent'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'ticket_number' => 'TKT' . time() . rand(1000, 9999),
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority ?? 'medium',
            'status' => 'open'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Support ticket created successfully',
            'data' => $ticket
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $tickets = $user->tickets()->latest()->paginate(20);
        return response()->json($tickets);
    }

    public function show($id): JsonResponse
    {
        $user = Auth::user();
        $ticket = $user->tickets()->findOrFail($id);
        return response()->json($ticket);
    }
}
