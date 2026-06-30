<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $services = Service::with('category')
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(25);

        return response()->json($services);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:1',
            'max_quantity' => 'required|integer|min:1|gte:min_quantity',
            'status' => 'required|in:active,inactive,maintenance',
            'provider_service_id' => 'nullable|string',
            'provider_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service = Service::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'min_quantity' => $request->min_quantity,
            'max_quantity' => $request->max_quantity,
            'status' => $request->status,
            'provider_service_id' => $request->provider_service_id,
            'provider_name' => $request->provider_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => $service
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $service = Service::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'min_quantity' => 'sometimes|integer|min:1',
            'max_quantity' => 'sometimes|integer|min:1|gte:min_quantity',
            'status' => 'sometimes|in:active,inactive,maintenance',
            'provider_service_id' => 'nullable|string',
            'provider_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }

    public function bulkImport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'services' => 'required|array',
            'services.*.name' => 'required|string',
            'services.*.category' => 'required|string',
            'services.*.price' => 'required|numeric',
            'services.*.min_quantity' => 'integer|min:1',
            'services.*.max_quantity' => 'integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imported = 0;
        $failed = 0;

        foreach ($request->services as $serviceData) {
            try {
                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($serviceData['category'])],
                    ['name' => $serviceData['category']]
                );

                Service::create([
                    'category_id' => $category->id,
                    'name' => $serviceData['name'],
                    'slug' => Str::slug($serviceData['name']),
                    'description' => $serviceData['description'] ?? null,
                    'price' => $serviceData['price'],
                    'min_quantity' => $serviceData['min_quantity'] ?? 10,
                    'max_quantity' => $serviceData['max_quantity'] ?? 10000,
                    'status' => 'active'
                ]);

                $imported++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Imported {$imported} services. {$failed} failed.",
            'data' => [
                'imported' => $imported,
                'failed' => $failed
            ]
        ]);
    }
}
