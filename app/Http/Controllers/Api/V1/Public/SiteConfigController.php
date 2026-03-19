<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SiteConfigController extends Controller
{
    public function show(): JsonResponse
    {
        $version = SiteSetting::settingsVersion();
        $cacheKey = sprintf('site_config:v%d', $version);

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function (): array {
            return [
                'maintenance' => SiteSetting::maintenanceConfig(),
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }
}
