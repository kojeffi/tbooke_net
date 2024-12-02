<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Get user settings and notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
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
            $messageNotifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();
            $messageNotificationCount = $messageNotifications->count();

            return response()->json([
                'user' => $user,
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
                'messageNotifications' => $messageNotifications,
                'messageNotificationCount' => $messageNotificationCount,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching settings: ' . $e->getMessage());
            return response()->json([
                'error' => 'Could not fetch settings. Please try again later.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the user profile settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Validate the password only if it is filled
            $request->validate([
                'first_name' => 'sometimes|string|max:255',
                'surname' => 'sometimes|string|max:255',
                'password' => 'sometimes|string|min:8|confirmed',
            ]);

            // Update the password if provided
            if ($request->filled('password')) {
                $user->password = bcrypt($request->input('password'));
            }

            // Update the first name if provided
            if ($request->filled('first_name')) {
                $user->first_name = $request->input('first_name');
            }

            // Update the surname if provided
            if ($request->filled('surname')) {
                $user->surname = $request->input('surname');
            }

            // Save the updated user details
            $user->save();

            return response()->json([
                'success' => 'Profile updated successfully',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json([
                'error' => 'Profile update failed. Please try again.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
