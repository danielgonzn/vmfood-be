<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload['slug'] = $payload['slug'] ?? Str::slug($payload['name']);
        $payload['sort_order'] = $payload['sort_order'] ?? 0;
        $payload['is_active'] = $payload['is_active'] ?? true;

        $category = Category::query()->create($payload);

        return response()->json([
            'message' => 'Categoría creada correctamente.',
            'data' => CategoryResource::make($category),
        ], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('categories', 'slug')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($payload['slug']) && array_key_exists('name', $payload)) {
            $payload['slug'] = Str::slug($payload['name']);
        }

        $category->update($payload);

        return response()->json([
            'message' => 'Categoría actualizada correctamente.',
            'data' => CategoryResource::make($category),
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $productsCount = $category->products()->count();

        if ($productsCount > 0) {
            return response()->json([
                'message' => "No se puede eliminar la categoría porque tiene {$productsCount} producto(s) asociado(s).",
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente.',
        ]);
    }
}
