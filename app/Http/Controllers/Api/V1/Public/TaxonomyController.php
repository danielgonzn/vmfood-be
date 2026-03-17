<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BrandResource;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class TaxonomyController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function brands(): JsonResponse
    {
        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => BrandResource::collection($brands),
        ]);
    }
}
