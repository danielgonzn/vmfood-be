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
            ->when($request->filled('category'), function ($builder) use ($request): void {
                $category = trim((string) $request->string('category'));
                $builder->whereHas('category', function ($relation) use ($category): void {
                    $relation->where('slug', $category)
                        ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($category)]);
                });
            })
            ->when($request->filled('subcategory'), function ($builder) use ($request): void {
                $subcategory = trim((string) $request->string('subcategory'));
                $builder->whereHas('category', function ($relation) use ($subcategory): void {
                    $relation->where('slug', $subcategory)
                        ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($subcategory)]);
                });
            })
            ->when($request->filled('brand'), function ($builder) use ($request): void {
                $brand = trim((string) $request->string('brand'));
                $builder->whereHas('brand', function ($relation) use ($brand): void {
                    $relation->where('slug', $brand)
                        ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($brand)]);
                });
            })
            ->when($request->filled('origin'), fn ($builder) => $builder->whereRaw('LOWER(origin) = ?', [mb_strtolower((string) $request->string('origin'))]))
            ->when($request->filled('condition'), function ($builder) use ($request): void {
                $conditionRaw = mb_strtolower(trim((string) $request->string('condition')));

                if (in_array($conditionRaw, ['usada', 'used'], true)) {
                    $builder->where('condition', 'Usada');

                    return;
                }

                if (in_array($conditionRaw, ['nueva', 'new'], true)) {
                    $builder->where('condition', 'Nueva');
                }
            })
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
