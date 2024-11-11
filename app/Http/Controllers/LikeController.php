<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\PostLikedMail;
use Illuminate\Support\Facades\Mail;

class LikeController extends Controller
{
    public function likePost($postId)
    {
        $post = Post::findOrFail($postId);
        $user = Auth::user();
    
        // Check if the user hasn't liked the post already
        if (!$user->likes()->where('post_id', $postId)->exists()) {
            // Attach the like to the post
            $user->likes()->attach($postId);
        }
    
        // Get the updated like count
        $likesCount = $post->likes()->count();

        //Mail::to($post->user->email)->send(new PostLikedMail($post, $user));

        // Return JSON response with the updated like count
        return response()->json(['likesCount' => $likesCount]);
    }   

    public function unlikePost($postId)
    {
        $post = Post::findOrFail($postId);
        $user = Auth::user();

        // Check if the user has liked the post
        if ($user->likes()->where('post_id', $postId)->exists()) {
            // Detach the like from the post
            $user->likes()->detach($postId);
        }

        // Get the updated like count
        $likesCount = $post->likes()->count();

        // Return JSON response with the updated like count
        return response()->json(['likesCount' => $likesCount]);
    }
}
