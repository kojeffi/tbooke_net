<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Notification;
use App\Models\BlueboardPost;
use Exception;

class TbookeBlueboardController extends Controller
{
    // Method to get notifications and blueboard posts
    public function tbookeBlueboard(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Get notifications for new connections
            $notifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Connection')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();

            // Count new connection notifications
            $notificationCount = $notifications->count();

            // Get notifications for new messages
            $messagenotifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();

            // Count new message notifications
            $messagenotificationCount = $messagenotifications->count();

            // Get Blueboard posts
            $posts = BlueboardPost::with('user')->latest()->get();

            // Return JSON response
            return response()->json([
                'user' => $user,
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
                'messagenotifications' => $messagenotifications,
                'messagenotificationCount' => $messagenotificationCount,
                'posts' => $posts,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving data.'], 500);
        }
    }

    // Method to create a new post (form view not necessary in API)
    public function create()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Return user info (or any other relevant info)
            return response()->json(['user' => $user]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving user data.'], 500);
        }
    }

    // Method to store a new Blueboard post
    public function store(Request $request)
    {
        try {
            // Validate incoming request
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            // Create a new Blueboard post
            $post = BlueboardPost::create([
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => auth()->id(),
            ]);

            // Return success response
            return response()->json([
                'message' => 'Your Blueboard Post has been created successfully',
                'post' => $post,
            ], 201); // 201 Created

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422); // 422 Unprocessable Entity
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the post.'], 500);
        }
    }
}
