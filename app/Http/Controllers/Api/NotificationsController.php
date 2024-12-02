<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NotificationsController extends Controller
{
    /**
     * Display a listing of notifications.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $currentUserId = Auth::id();

            // Get unread notifications
            $notifications = Notification::with('sender')
                ->where('user_id', $currentUserId)
                ->where('type', 'New Connection')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();

            $notificationCount = $notifications->count();

            // Get all notifications
            $notificationsPage = Notification::with('sender')
                ->where('user_id', $currentUserId)
                ->where('type', 'New Connection')
                ->orderByDesc('created_at')
                ->get();

            // Get unread message notifications
            $messageNotifications = Notification::with('sender')
                ->where('user_id', $currentUserId)
                ->where('type', 'New Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();

            $messageNotificationCount = $messageNotifications->count();

            return response()->json([
                'user' => Auth::user(),
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
                'notificationsPage' => $notificationsPage,
                'messageNotifications' => $messageNotifications,
                'messageNotificationCount' => $messageNotificationCount,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch notifications'], 500);
        }
    }

    /**
     * Mark notifications as read.
     *
     * @return JsonResponse
     */
    public function markAsRead(): JsonResponse
    {
        try {
            $userId = Auth::id();
            Notification::markAsRead($userId);

            return response()->json(['message' => 'Notifications marked as read'], 200);
        } catch (\Exception $e) {
            Log::error('Error marking notifications as read: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark notifications as read'], 500);
        }
    }

    /**
     * Mark message notifications as read.
     *
     * @return JsonResponse
     */
    public function messagesMarkAsRead(): JsonResponse
    {
        try {
            $userId = Auth::id();
            Notification::messagesMarkAsRead($userId);

            return response()->json(['message' => 'Message notifications marked as read'], 200);
        } catch (\Exception $e) {
            Log::error('Error marking message notifications as read: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark message notifications as read'], 500);
        }
    }

    /**
     * Delete a notification.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $notification = Notification::findOrFail($id);
            
            if ($notification->delete()) {
                return response()->json(['success' => true, 'message' => 'Notification deleted successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete notification'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete notification'], 500);
        }
    }
}
