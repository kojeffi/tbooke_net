<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use App\Models\TbookeLearning; 
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends Controller
{
    public function show($slug): JsonResponse
    {
        try {
            $user = Auth::user();

            // Fetch the content by slug
            $content = TbookeLearning::where('slug', $slug)->firstOrFail();

            // Convert comma-separated categories to array
            $categories = explode(',', $content->content_category);

            // Fetch related content
            $relatedContent = TbookeLearning::where('id', '!=', $content->id)
                ->where(function ($query) use ($categories) {
                    foreach ($categories as $category) {
                        $query->orWhere('content_category', 'LIKE', '%' . $category . '%');
                    }
                })
                ->take(4) // Limit the number of related content items
                ->get();

            // Get notifications for New Connections
            $notifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Connection')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();
            $notificationCount = $notifications->count();

            // Get notifications for New Messages
            $messagenotifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();
            $messagenotificationCount = $messagenotifications->count();

            return response()->json([
                'user' => $user,
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
                'content' => $content,
                'messagenotifications' => $messagenotifications,
                'messagenotificationCount' => $messagenotificationCount,
                'relatedContent' => $relatedContent,
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Content not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while processing your request.',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
