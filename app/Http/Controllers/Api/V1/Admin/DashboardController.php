<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => [
                'products_total' => Product::query()->count(),
                'products_published' => Product::query()->published()->count(),
                'inquiries_new' => Inquiry::query()->where('status', 'new')->count(),
                'admins_total' => User::query()->where('is_admin', true)->count(),
            ],
        ]);
    }
}
