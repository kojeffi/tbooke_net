<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        try {
            // Authenticate the user
            $request->authenticate();

            // Return success response
            return response()->json([
                'message' => 'Login successful.',
                'user' => $request->user(),
                'token' => $request->user()->createToken('authToken')->plainTextToken, // Generate token for API authentication
            ], 200);
        } catch (\Exception $e) {
            // Log the error message
            Log::error($e->getMessage()); // Log the error

            // Return a JSON error response
            return response()->json([
                'error' => 'Login failed.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            // Logout the user
            Auth::guard('api')->logout();

            // Return success response
            return response()->json([
                'message' => 'Logout successful.',
            ], 200);
        } catch (\Exception $e) {
            // Log the error message
            Log::error($e->getMessage()); // Log the error

            // Return a JSON error response
            return response()->json([
                'error' => 'Logout failed.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
