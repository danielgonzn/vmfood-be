<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function image(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $disk = env('MEDIA_DISK', 'public');
        $path = $payload['image']->store('products', $disk);
        $url = Storage::disk($disk)->url($path);

        return response()->json([
            'message' => 'Imagen cargada correctamente.',
            'data' => [
                'path' => $path,
                'url' => $url,
                'disk' => $disk,
            ],
        ], 201);
    }
}
