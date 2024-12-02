<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Group;
use App\Models\GroupPost;
use App\Models\GroupComment;
use App\Models\GroupLike;
use App\Models\GroupRepost;
use App\Models\GroupMember;
use App\Mail\SendToGroup;

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
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi,webm|max:20480', // Adjust media rules as needed
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

        $post2 = GroupPost::where('id', $post->id)->first();
        $members = GroupMember::join('users', 'users.id', '=', 'group_members.user_id')
        ->where('group_id', $post2->group_id)->get();
        $user = auth()->user();
        //send to members
        foreach ($members as  $member) {
           Mail::to($member->email)->send(new SendToGroup($post2, $user,'New Post!',4,$member));
        }

    
        // Redirect back with success message or return AJAX response
        return response()->json(['success' => true]);
    }

    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

       $post = GroupComment::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        // $post2 = GroupPost::where('id', $postId)->first();
        // $members = GroupMember::join('users', 'users.id', '=', 'group_members.user_id')
        // ->where('group_id', $post2->group_id)->get();
        // $user = auth()->user();
        // //send to members
        // foreach ($members as  $member) {
        //    Mail::to($member->email)->send(new SendToGroup($post2, $user,'New Post!',3,$member));
        // }

        return back()->with('success', 'Comment posted successfully');
    }

    public function likePost($postId)
    {
        GroupLike::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
        ]);
        
        // $post2 = GroupPost::where('id', $postId)->first();
        // $members = GroupMember::join('users', 'users.id', '=', 'group_members.user_id')
        // ->where('group_id', $post2->group_id)->get();
        // $user = auth()->user();
        // //send to members
        // foreach ($members as  $member) {
        //    Mail::to($member->email)->send(new SendToGroup($post2, $user,'New Post!',2,$member));
        // }

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

        // $post2 = GroupPost::where('id', $postId)->first();
        // $members = GroupMember::join('users', 'users.id', '=', 'group_members.user_id')
        // ->where('group_id', $post2->group_id)->get();
        // $user = auth()->user();
        // //send to members
        // foreach ($members as  $member) {
        //    Mail::to($member->email)->send(new SendToGroup($post2, $user,'New Post!',1,$member));
        // }
  
        return back()->with('success', 'Post reposted');
    }


    
}

