<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 12);

        $query = Product::query()
            ->with(['category', 'brand'])
            ->published()
            ->when($request->filled('search'), function ($builder) use ($request): void {
                $search = mb_strtolower((string) $request->string('search'));
                $builder->where(function ($nested) use ($search): void {
                    $nested->whereRaw('LOWER(title) like ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(description) like ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(short_description) like ?', ["%{$search}%"]);
                });
            })
            ->when($request->filled('category'), fn ($builder) => $builder->whereHas('category', fn ($category) => $category->where('slug', $request->string('category'))))
            ->when($request->filled('brand'), fn ($builder) => $builder->whereHas('brand', fn ($brand) => $brand->where('slug', $request->string('brand'))))
            ->when($request->filled('available'), fn ($builder) => $builder->where('available', filter_var($request->input('available'), FILTER_VALIDATE_BOOL)))
            ->orderBy('title');

        $products = $query->paginate(max(1, min($perPage, 60)));

        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::query()
            ->with(['category', 'brand'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => ProductResource::make($product),
        ]);
    }
}
