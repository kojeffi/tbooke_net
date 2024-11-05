<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Creator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CreatorController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $firstName = $user->first_name;
            $surname = $user->surname;

            $validatedData = $request->validate([
                'creator_subjects' => 'required|array',
                'creator_subjects.*' => 'string',
                'creator_expertise' => 'required|array',
                'creator_expertise.*' => 'string',
                'the_why' => 'required|string',
            ]);

            // Convert arrays to comma-separated strings
            $creator_subjects = implode(',', $validatedData['creator_subjects']);
            $creator_expertise = implode(',', $validatedData['creator_expertise']);

            // Create a new Creator instance and save it
            $creator = new Creator();
            $creator->creator_subjects = $creator_subjects;
            $creator->creator_expertise = $creator_expertise;
            $creator->the_why = $validatedData['the_why'];
            $creator->first_name = $firstName;
            $creator->surname = $surname;
            $creator->user_id = auth()->id();
            $creator->save();

            return response()->json([
                'message' => 'Application done successfully',
                'redirect_url' => route('tbooke-learning'),
            ], Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'messages' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while processing your request.',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Other methods can be added based on your application requirements
}
