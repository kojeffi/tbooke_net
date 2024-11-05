<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class FeedController extends Controller
{
    public function feeds(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // Fetch all posts with relationships
            $posts = Post::with([
                'comments' => function ($query) {
                    $query->orderByDesc('created_at'); // Ensure comments are ordered by created_at descending
                },
                'user',
                'reposter',
                'originalUser',
                'originalPost',
            ])
            ->orderByDesc('created_at') // Order posts by latest created_at
            ->get();

            // Separate reposts from original posts
            $repostedPosts = $posts->where('is_repost', true);
            $originalPosts = $posts->where('is_repost', false);

            // Prepare collection for combined posts
            $combinedPosts = collect();

            // Add original posts to the combined collection
            foreach ($originalPosts as $post) {
                $post->is_repost = false;
                $combinedPosts->push($post);
            }

            // Add reposted posts to the combined collection
            foreach ($repostedPosts as $repostedPost) {
                $originalPost = Post::find($repostedPost->original_post_id);
                if ($originalPost) {
                    $originalPost->is_repost = true;
                    $originalPost->reposter = $repostedPost->user;
                    $originalPost->repost_timestamp = $repostedPost->created_at;

                    // Sort comments for originalPost by created_at descending
                    $originalPost->comments = $originalPost->comments->sortByDesc('created_at');

                    $combinedPosts->push($originalPost);
                }
            }

            // Sort the combined collection by the correct timestamp (created_at or repost_timestamp)
            $combinedPosts = $combinedPosts->sortByDesc(function ($post) {
                return $post->is_repost ? $post->repost_timestamp : $post->created_at;
            });

            // Get notifications
            $notifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Connection')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();
            $notificationCount = $notifications->count();

            // Get message notifications
            $messagenotifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();
            $messagenotificationCount = $messagenotifications->count();

            $adminnotifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Admin Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();

            // Calculate the counts for each type of message
            $adminnotificationCount = $adminnotifications->count();

            // Total notification count
            $totalMessageNotificationCount = $messagenotificationCount + $adminnotificationCount;

            // Prepare the response data
            $responseData = [
                'user' => $user,
                'posts' => $combinedPosts,
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
                'messagenotificationCount' => $messagenotificationCount,
                'adminnotifications' => $adminnotifications,
                'adminnotificationCount' => $adminnotificationCount,
                'totalMessageNotificationCount' => $totalMessageNotificationCount,
            ];

            return response()->json($responseData, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching the feeds.',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
