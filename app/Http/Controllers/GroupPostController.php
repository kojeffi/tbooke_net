<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupPost;
use App\Models\GroupComment;
use App\Models\GroupLike;
use App\Models\GroupRepost;

class GroupPostController extends Controller
{
    // Post into a group
    public function store(Request $request, $slug)
    {
        // Find the group by slug
        $group = Group::where('slug', $slug)->firstOrFail();
    
        // Validate the request
        $request->validate([
            'content' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480', // Adjust media rules as needed
        ]);
    
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
    
        // Redirect back with success message or return AJAX response
        return response()->json(['success' => true]);
    }

    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        GroupComment::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Comment posted successfully');
    }

    public function likePost($postId)
    {
        GroupLike::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Post liked');
    }

    public function repostPost(Request $request, $postId)
    {
        $request->validate([
            'content' => 'nullable|string|max:1000',
        ]);

        GroupRepost::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Post reposted');
    }


    
}

