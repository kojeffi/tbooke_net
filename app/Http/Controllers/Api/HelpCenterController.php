<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HelpCenterController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();

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

            // Get admin notifications
            $adminnotifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Admin Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();
            $adminnotificationCount = $adminnotifications->count();

            // Total notification count
            $totalMessageNotificationCount = $messagenotificationCount + $adminnotificationCount;

            return response()->json([
                'user' => $user,
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
                'messagenotifications' => $messagenotifications,
                'messagenotificationCount' => $messagenotificationCount,
                'adminnotifications' => $adminnotifications,
                'adminnotificationCount' => $adminnotificationCount,
                'totalMessageNotificationCount' => $totalMessageNotificationCount,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching notifications.',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
