<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $services = Service::with('category')
            ->when($request->category, function ($query, $category) {
                return $query->where('category_id', $category);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%");
            })
            ->where('status', 'active')
            ->paginate(20);

        return response()->json($services);
    }

    public function show($id): JsonResponse
    {
        $service = Service::with('category')->findOrFail($id);
        return response()->json($service);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::where('status', true)->orderBy('order')->get();
        return response()->json($categories);
    }
}
