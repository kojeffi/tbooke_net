<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Models\Post;


class CommentController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'post_id' => 'required',
            'content' => 'required',
        ]);

        // Create a new comment
        $comment = new Comment();
        $comment->content = $validatedData['content'];
        $comment->post_id = $validatedData['post_id'];
        $comment->user_id = auth()->id();
        $comment->save();

        return response()->json(['message' => 'Comment created successfully'], 200);
    }
    
}
