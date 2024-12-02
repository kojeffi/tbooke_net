<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupPost;
use App\Models\GroupComment;
use App\Models\GroupLike;
use App\Models\GroupRepost;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class GroupPostController extends Controller
{
    // Post into a group
    public function store(Request $request, $slug)
    {
        // Find the group by slug
        try {
            $group = Group::where('slug', $slug)->firstOrFail();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480', // Adjust media rules as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Create the post
            $post = new GroupPost();
            $post->group_id = $group->id;
            $post->user_id = auth()->id();
            $post->content = $request->input('content');

            // Handle media upload if present
            if ($request->hasFile('media')) {
                $mediaPath = $request->file('media')->store('group_posts_media', 'public');
                $post->media = $mediaPath;
            }

            $post->save();

            return response()->json(['success' => true, 'post' => $post], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to create post'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Store a comment on a post
    public function storeComment(Request $request, $postId)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            GroupComment::create([
                'post_id' => $postId,
                'user_id' => auth()->id(),
                'content' => $request->content,
            ]);

            return response()->json(['message' => 'Comment posted successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to post comment'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Like a post
    public function likePost($postId)
    {
        try {
            GroupLike::create([
                'post_id' => $postId,
                'user_id' => auth()->id(),
            ]);

            return response()->json(['message' => 'Post liked'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to like post'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Repost a post
    public function repostPost(Request $request, $postId)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'content' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            GroupRepost::create([
                'post_id' => $postId,
                'user_id' => auth()->id(),
                'content' => $request->content,
            ]);

            return response()->json(['message' => 'Post reposted'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to repost'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
