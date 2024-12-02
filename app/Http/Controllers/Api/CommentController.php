<?php

namespace App\Http\Controllers\Api;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'post_id' => 'required|exists:posts,id', // Ensure the post_id exists
                'content' => 'required|string|max:500', // Add a maximum length for content
            ]);

            // Create a new comment
            $comment = Comment::create([
                'content' => $validatedData['content'],
                'post_id' => $validatedData['post_id'],
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Comment created successfully',
                'comment' => $comment // Optionally return the created comment
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'messages' => $e->validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while processing your request.',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
