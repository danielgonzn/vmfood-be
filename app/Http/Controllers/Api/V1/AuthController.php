<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated(), true)) {
            return response()->json([
                'message' => 'Credenciales inválidas.',
            ], 422);
        }

        $request->session()->regenerate();
        $request->user()?->update(['remember_token' => null]);

        return response()->json([
            'message' => 'Sesión iniciada correctamente.',
            'data' => [
                'id' => $request->user()?->id,
                'name' => $request->user()?->name,
                'email' => $request->user()?->email,
                'is_admin' => (bool) $request->user()?->is_admin,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $request->user()?->id,
                'name' => $request->user()?->name,
                'email' => $request->user()?->email,
                'is_admin' => (bool) $request->user()?->is_admin,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Sesión finalizada.',
        ]);
    }
}
