<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class HelpCenterController extends Controller
{
    public function index(){
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
        
        return view('help-center', compact(
            'user', 'notifications',
            'notificationCount', 'messagenotifications', 'messagenotificationCount', 'adminnotifications', 'adminnotificationCount', 'totalMessageNotificationCount'
        ));
    }
}
