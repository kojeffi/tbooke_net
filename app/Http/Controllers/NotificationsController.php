<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class NotificationsController extends Controller
{

    public function index()
    {
            $user = Auth::user();

            // Get notifications
            $notificationspage = Notification::with('sender')
            ->where('user_id', auth()->user()->id)
            ->where('type', 'New Connection')
            ->orderByDesc('created_at')
            ->get();
    

        return view('notifications', compact('notificationspage', 'user'));
    }

    //notification mark as read
    public function markAsRead()
    {
        $userId = Auth::id();
        Notification::markAsRead($userId);

        return response()->json(['message' => 'Notifications marked as read']);
    }

      //messagenotification mark as read
      public function messagesmarkAsRead()
      {
          $userId = Auth::id();
          Notification::messagesmarkAsRead($userId);
  
          return response()->json(['message' => 'Notifications marked as read']);
      }

      //delete notification

      public function destroy($id)
      {
          $notification = Notification::findOrFail($id);
          if ($notification->delete()) {
              return response()->json(['success' => true]);
          } else {
              return response()->json(['success' => false], 500);
          }
      }
      


}
