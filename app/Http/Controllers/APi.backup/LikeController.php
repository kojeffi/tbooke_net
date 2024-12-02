<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\PostLikedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class LikeController extends Controller
{
    public function likePost($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            $user = Auth::user();

            // Check if the user has already liked the post
            if (!$user->likes()->where('post_id', $postId)->exists()) {
                $user->likes()->attach($postId);
                $likesCount = $post->likes()->count();

                // Send email notification to the post creator
                Mail::to($post->user->email)->send(new PostLikedMail($post, $user));

                return response()->json([
                    'message' => 'Post liked successfully.',
                    'likesCount' => $likesCount,
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Post already liked.',
                ], Response::HTTP_CONFLICT);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Post not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while liking the post.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function unlikePost($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            $user = Auth::user();

            // Check if the user has liked the post
            if ($user->likes()->where('post_id', $postId)->exists()) {
                $user->likes()->detach($postId);
                $likesCount = $post->likes()->count();

                return response()->json([
                    'message' => 'Post unliked successfully.',
                    'likesCount' => $likesCount,
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Post not liked yet.',
                ], Response::HTTP_CONFLICT);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Post not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while unliking the post.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
