<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Mail\FollowNotificationMail;
use Illuminate\Support\Facades\Mail;

class FollowerController extends Controller
{
  public function follow(User $user)
  {
      $follower = auth()->user();
      $follower->followings()->attach($user);
  
      $message =  'started following you.';
      $type = 'New Connection';
      $follower_name = $follower->first_name .' '. $follower->surname;
      
      // Create a notification for the user being followed
      Notification::create([
          'user_id' => $user->id,
          'sender_id' => $follower->id,
          'type' => $type,
          'follower_name' => $follower_name,
          'message' => $message,
      ]);
  
      // Send the email notification
      Mail::to($user->email)->send(new FollowNotificationMail($follower, $user));
  
      return response()->json(['message' => 'Followed successfully'], 200);
  }

    public function unfollow (User $user)
    {
      $follower = auth()->user();
      $follower->followings()->detach($user);

      return response()->json(['message' => 'Unfollowed successfully'], 200);
    }
}
