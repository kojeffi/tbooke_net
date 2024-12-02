<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class SchoolController extends Controller
{
    /**
     * Get the schools corner data along with notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function schoolsCorner(Request $request)
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
    
            // Fetch all schools
            $schools = School::all();
    
            return response()->json([
                'user' => $user,
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
                'schools' => $schools,
                'messageNotifications' => $messageNotifications,
                'messageNotificationCount' => $messageNotificationCount,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching schools corner: ' . $e->getMessage());
            return response()->json([
                'error' => 'Could not fetch schools corner. Please try again later.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new school.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        // No need for a specific implementation here as we don't return views
        return response()->json(['message' => 'Use POST /schools to create a new school.'], 200);
    }

    /**
     * Store a newly created school.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            // Handle file upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('schools-corner', 'public');
                $validatedData['thumbnail'] = $imagePath; // Use the correct field name here
            } else {
                $validatedData['thumbnail'] = 'default-thumbnail.jpg'; // Set a default value if necessary
            }
    
            // Create the school
            $school = School::create($validatedData);
    
            return response()->json([
                'success' => 'Your school has been added successfully.',
                'school' => $school,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating school: ' . $e->getMessage());
            return response()->json([
                'error' => 'School creation failed. Please try again.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified school.
     *
     * @param  School  $school
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(School $school)
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
                'school' => $school,
                'messageNotifications' => $messageNotifications,
                'messageNotificationCount' => $messageNotificationCount,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching school: ' . $e->getMessage());
            return response()->json([
                'error' => 'Could not fetch school details. Please try again later.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
