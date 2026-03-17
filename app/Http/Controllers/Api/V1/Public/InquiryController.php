<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\InquiryStoreRequest;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;

class InquiryController extends Controller
{
    public function store(InquiryStoreRequest $request): JsonResponse
    {
        $inquiry = Inquiry::query()->create($request->validated());

        return response()->json([
            'message' => 'Consulta recibida correctamente.',
            'data' => [
                'id' => $inquiry->id,
                'status' => $inquiry->status,
            ],
        ], 201);
    }
}
