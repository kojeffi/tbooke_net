<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\BlueboardPost;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;

class TbookeBlueboardController extends Controller
{
    public function tbookeBlueboard(Request $request)
    {
        $user = Auth::user();

        // Get Blueboard posts
        $posts = BlueboardPost::with('user')->latest()->get();

        return view('tbooke-blueboard', compact('user', 'posts'));
    }

    public function create()
    {
        $user = Auth::user();
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->user()->id)
            ->where('read', 0)
            ->orderByDesc('created_at')
            ->get();
        $notificationCount = $notifications->count();
        return view('tbooke-blueboard.create', compact('user', 'notifications', 'notificationCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        BlueboardPost::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        Alert::Success('Your Blueboard Post has been created successfully');
        return redirect()->route('tbooke-blueboard');
    }

    public function userPosts($username)
    {
        // Fetch the user by username
        $user = User::where('username', $username)->firstOrFail();

        // Fetch posts created by this user
        $posts = BlueboardPost::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Return the view with posts and user info
        return view('tbooke-blueboard.user-posts', compact('posts', 'user', 'username'));
    }

    public function edit($username, $id)
    {
        $user = Auth::user();
        $post = BlueboardPost::findOrFail($id);

        // Ensure the post belongs to the user
        if ($post->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('tbooke-blueboard.edit', compact('post', 'username', 'user'));
    }



    public function destroy($id)
    {
        $post = BlueboardPost::findOrFail($id);

        // Check if the authenticated user is the owner
        if (auth()->user()->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $post->delete();

        return redirect()->route('blueboard.userPosts', auth()->user()->username)->with('success', 'Post deleted successfully.');
    }

    public function update(Request $request, $username, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = BlueboardPost::findOrFail($id);

        // Ensure the post belongs to the user
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Update the post
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        Alert::success('Your Blueboard Post has been updated successfully');
        return redirect()->route('blueboard.userPosts', $username);
    }




}

