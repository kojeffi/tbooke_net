<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use App\Mail\PostRepostedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * Store a new post.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
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
                $post->media_path = json_encode($mediaPaths); // Store as JSON
            }

            $post->save();

            return response()->json(['message' => 'Post created successfully'], 201);
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create post', 'details' => $e->getMessage()], 500);
        }
    }    
    
    /**
     * Repost an existing post.
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function repostPost(Request $request, Post $post): JsonResponse
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

        return response()->json(['message' => 'Post reposted successfully!'], 200);
    }

    /**
     * Delete a post.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);
            
            if ($post->delete()) {
                return response()->json(['success' => true, 'message' => 'Post deleted successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete post'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete post', 'details' => $e->getMessage()], 500);
        }
    }
}
