<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => BrandResource::collection($brands),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:brands,slug'],
            'country' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload['slug'] = $payload['slug'] ?? Str::slug($payload['name']);
        $payload['is_active'] = $payload['is_active'] ?? true;

        $brand = Brand::query()->create($payload);

        return response()->json([
            'message' => 'Marca creada correctamente.',
            'data' => BrandResource::make($brand),
        ], 201);
    }

    public function update(Request $request, Brand $brand): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('brands', 'slug')->ignore($brand->id)],
            'country' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($payload['slug']) && array_key_exists('name', $payload)) {
            $payload['slug'] = Str::slug($payload['name']);
        }

        $brand->update($payload);

        return response()->json([
            'message' => 'Marca actualizada correctamente.',
            'data' => BrandResource::make($brand),
        ]);
    }

    public function destroy(Brand $brand): JsonResponse
    {
        $productsCount = $brand->products()->count();

        if ($productsCount > 0) {
            return response()->json([
                'message' => "No se puede eliminar la marca porque tiene {$productsCount} producto(s) asociado(s).",
            ], 422);
        }

        $brand->delete();

        return response()->json([
            'message' => 'Marca eliminada correctamente.',
        ]);
    }
}
