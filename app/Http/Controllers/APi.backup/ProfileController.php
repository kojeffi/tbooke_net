<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Creator;
use App\Models\User;
use App\Models\Notification;
use App\Models\Subject;
use App\Models\Topic;

class ProfileController extends Controller
{
    public function dashboard()
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

            $messagenotifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();

            $adminnotifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Admin Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'user' => $user,
                'notifications' => $notifications,
                'notificationCount' => $notifications->count(),
                'messagenotifications' => $messagenotifications,
                'messagenotificationCount' => $messagenotifications->count(),
                'adminnotifications' => $adminnotifications,
                'adminnotificationCount' => $adminnotifications->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch dashboard data', 'message' => $e->getMessage()], 500);
        }
    }

    // Show the user's profile.
    public function showOwn()
    {
        try {
            $user = Auth::user();
            $profileDetails = null;

            // Get the user's profile details based on their profile type
            switch ($user->profile_type) {
                case 'teacher':
                    $profileDetails = $user->teacherDetails;
                    break;
                case 'student':
                    $profileDetails = $user->studentDetails;
                    break;
                case 'institution':
                    $profileDetails = $user->institutionDetails;
                    break;
                case 'other':
                    $profileDetails = $user->otherDetails;
                    break;
            }

            if ($profileDetails->socials) {
                $profileDetails->socials = json_decode($profileDetails->socials, true);
            }

            $posts = $user->posts()->orderBy('created_at', 'desc')->get();
            $followersCount = $user->followers()->count();

            return response()->json([
                'user' => $user,
                'profileDetails' => $profileDetails,
                'posts' => $posts,
                'followersCount' => $followersCount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch profile data', 'message' => $e->getMessage()], 500);
        }
    }

    // Show the edit profile form.
    public function edit()
    {
        try {
            $user = Auth::user();
            $subjects = Subject::all();
            $topics = Topic::all();

            $profileDetails = null;
            switch ($user->profile_type) {
                case 'teacher':
                    $profileDetails = $user->teacherDetails;
                    break;
                case 'student':
                    $profileDetails = $user->studentDetails;
                    break;
                case 'institution':
                    $profileDetails = $user->institutionDetails;
                    break;
                case 'other':
                    $profileDetails = $user->otherDetails;
                    break;
            }

            if ($profileDetails->socials) {
                $profileDetails->socials = json_decode($profileDetails->socials, true);
            }

            return response()->json([
                'user' => $user,
                'profileDetails' => $profileDetails,
                'subjects' => $subjects,
                'topics' => $topics,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch edit profile data', 'message' => $e->getMessage()], 500);
        }
    }

    // Update the user's profile.
    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            $user->update($request->only('first_name', 'surname', 'email'));

            // Handle profile picture update
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                $file = $request->file('profile_picture');
                $fileName = 'profile_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('profile-images', $fileName, 'public');
                $user->profile_picture = $filePath;
                $user->save();
            }

            // Update social media links
            $profileDetails = $user->profileDetails();
            if ($profileDetails) {
                $profileDetails->socials = $request->input('socials');
                $profileDetails->about = $request->input('about');
                $profileDetails->save();
            }

            return response()->json(['message' => 'Profile updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update profile', 'message' => $e->getMessage()], 500);
        }
    }

    // Show the user's profile by username.
    public function show($username)
    {
        try {
            // Implement show logic here
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch user profile', 'message' => $e->getMessage()], 500);
        }
    }
}
