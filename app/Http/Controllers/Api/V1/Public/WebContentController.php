<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\WebContentItemResource;
use App\Models\WebContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WebContentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $section = trim((string) $request->string('section', ''));
        $key = trim((string) $request->string('key', ''));
        $version = (int) Cache::get('web_content_version', 1);
        $cacheKey = sprintf('web_content:v%d:section:%s:key:%s', $version, $section !== '' ? $section : 'all', $key !== '' ? $key : 'all');

        $items = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($section, $key) {
            $query = WebContent::query()
                ->where('is_active', true)
                ->where('is_published', true)
                ->where(function ($builder) {
                    $builder
                        ->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->orderBy('section')
                ->orderBy('sort_order')
                ->orderBy('id');

            if ($section !== '') {
                $query->where('section', $section);
            }

            if ($key !== '') {
                $query->where('key', $key);
            }

            return $query->get();
        });

        return response()->json([
            'data' => WebContentItemResource::collection($items),
        ]);
    }
}
