<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ConfirmablePasswordController extends Controller
{
    /**
     * Confirm the user's password.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the user's password
        if (! Auth::guard('api')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        // Store the confirmation time in the session
        $request->session()->put('auth.password_confirmed_at', time());

        // Return a successful response
        return response()->json(['message' => 'Password confirmed successfully.'], 200);
    }
}
