<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get notifications
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->user()->id)
            ->where('type', 'New Connection')
            ->where('read', 0)
            ->orderByDesc('created_at')
            ->get();
        $notificationCount = $notifications->count();
  
        // Get message notifications
        $messagenotifications = Notification::with('sender')
            ->where('user_id', auth()->user()->id)
            ->where('type', 'New Message')
            ->where('read', 0)
            ->orderByDesc('created_at')
            ->get();
        $messagenotificationCount = $messagenotifications->count();

        return view('settings', compact('user', 'notifications', 'notificationCount', 'messagenotifications', 'messagenotificationCount'));
    }

    /**
     * Update the user profile settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Validate the password only if it is filled
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|string|min:8|confirmed',
                ]);

                // Update the password if provided
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

            // Display success message
            Alert::success('Profile updated successfully');
            return redirect()->route('settings')->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Error updating profile: ' . $e->getMessage());
            Alert::error('Profile update failed');
            return redirect()->route('settings')->withErrors('Profile update failed. Please try again.');
        }
    }
}