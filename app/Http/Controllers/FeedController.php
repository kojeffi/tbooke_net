<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class FeedController extends Controller
{
    public function feeds(Request $request)
    {
        $user = Auth::user();
    
        // Fetch all posts with relationships while filtering out archived users
        $posts = Post::with([
            'comments' => function ($query) {
                // Ensure comments are ordered by created_at descending
                $query->orderByDesc('created_at')
                    // Exclude comments from users who have been deleted (archived)
                    ->whereHas('user', function ($query) {
                        $query->whereNull('deleted_at');
                    });
            },
            'user', // Fetch the post's user
            'reposter', // If it's a repost, fetch the reposter
            'originalUser', // If it's a repost, fetch the original post's user
            'originalPost' => function ($query) {
                $query->with([
                    'comments' => function ($query) {
                        // Exclude comments from deleted (archived) users on original posts
                        $query->whereHas('user', function ($query) {
                            $query->whereNull('deleted_at');
                        });
                    }
                ]);
            } // Fetch the original post and its comments, ensuring archived comments are excluded
        ])
        // Only fetch posts from non-archived users
        ->whereHas('user', function ($query) {
            $query->whereNull('deleted_at'); // Ensure that only posts from active (non-archived) users are fetched
        })
        // Exclude reposts if the original post's author is archived
        ->where(function ($query) {
            $query->where('is_repost', false) // Regular post
                ->orWhere(function ($query) {
                    $query->where('is_repost', true) // Repost case
                        ->whereHas('originalUser', function ($query) {
                            $query->whereNull('deleted_at'); // Only include reposts where the original user's deleted_at is null (non-archived)
                        });
                });
        })
        ->orderByDesc('created_at')
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
        $messagenotificationCount = $messagenotifications->count();
        $adminnotificationCount = $adminnotifications->count();
 
        // Total notification count
        $totalMessageNotificationCount = $messagenotificationCount + $adminnotificationCount;
       
        return view('feed', compact('user', 'posts', 'notifications', 'messagenotifications', 'notificationCount', 'messagenotificationCount', 'adminnotifications', 'adminnotificationCount', 'totalMessageNotificationCount'));
    }
}