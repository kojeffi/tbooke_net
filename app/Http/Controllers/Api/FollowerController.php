<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Notification;
use App\Mail\FollowNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FollowerController extends Controller
{
    public function follow(User $user): JsonResponse
    {
        try {
            $follower = auth()->user();

            // Check if already following
            if ($follower->followings()->where('followed_user_id', $user->id)->exists()) {
                return response()->json(['message' => 'You are already following this user.'], Response::HTTP_CONFLICT);
            }

            // Attach the user to the followings
            $follower->followings()->attach($user);

            $message = 'started following you.';
            $type = 'New Connection';
            $follower_name = $follower->first_name . ' ' . $follower->surname;

            // Create a notification for the user being followed
            Notification::create([
                'user_id' => $user->id,
                'sender_id' => $follower->id,
                'type' => $type,
                'follower_name' => $follower_name,
                'message' => $message,
            ]);

            // Send the email notification
            Mail::to($user->email)->send(new FollowNotificationMail($follower, $user));

            return response()->json(['message' => 'Followed successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while following the user.',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function unfollow(User $user): JsonResponse
    {
        try {
            $follower = auth()->user();

            // Check if not following
            if (!$follower->followings()->where('followed_user_id', $user->id)->exists()) {
                return response()->json(['message' => 'You are not following this user.'], Response::HTTP_CONFLICT);
            }

            // Detach the user from the followings
            $follower->followings()->detach($user);

            return response()->json(['message' => 'Unfollowed successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while unfollowing the user.',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
