<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Log; 
use App\Mail\PostRepostedMail;
use Illuminate\Support\Facades\Mail;


class PostController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'content' => 'nullable|string',
                'media_path.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:30240',
            ]);

            $post = new Post();
            $post->content = $validatedData['content'];
            $post->user_id = auth()->id();

            // Handle multiple file uploads if media is present
            if ($request->hasFile('media_path')) {
                $mediaPaths = [];
                foreach ($request->file('media_path') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('content-media', $fileName, 'public');
                    $mediaPaths[] = $filePath;
                }
                $post->media_path = $mediaPaths; // Assign array to the JSON attribute
            }

            $post->save();

            return response()->json(['message' => 'Post created successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create post'], 500);
        }
    }    
    
    

    public function repostPost(Request $request, Post $post)
{
    $user = auth()->user();

    // Check if the user has already reposted this post
    $alreadyReposted = Post::where('user_id', $user->id)
                             ->where('original_post_id', $post->id)
                             ->exists();

    if ($alreadyReposted) {
        return response()->json(['message' => 'You have already reposted this post.'], 400);
    }

    // Create a new post entry for the repost
    Post::create([
        'user_id' => $user->id,
        'content' => $post->content,
        'original_post_id' => $post->id, // Track the original post
        'original_user_id' => $post->user_id, // Track the original user
        'is_repost' => true,
        'media_path' => $post->media_path, // Include the media_path
    ]);

    // Increment the repost count on the original post
    $post->increment('repost_count');

    // Send email notification to the original post creator
    Mail::to($post->user->email)->send(new PostRepostedMail($post, $user));

    return response()->json(['message' => 'Post reposted successfully!']);
}

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        
        if ($post->delete()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 500);
        }
    }
    
}

