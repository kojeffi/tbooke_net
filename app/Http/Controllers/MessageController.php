<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\MessageReceived;

class MessageController extends Controller
{

    public function index($username = null)
    {
        $user = Auth::user();
        $currentUserId = Auth::id();
        $recipient = null;
        $messages = collect();
    
        // Load the recipient and messages if a username is specified
        if ($username) {
            $recipient = User::where('username', $username)->with('institutionDetails')->first();
    
            if ($recipient) {
                $messages = Message::where(function ($query) use ($currentUserId, $recipient) {
                    $query->where('sender_id', $currentUserId)
                        ->orWhere('receiver_id', $currentUserId);
                })
                ->where(function ($query) use ($recipient) {
                    $query->where('sender_id', $recipient->id)
                        ->orWhere('receiver_id', $recipient->id);
                })
                ->orderBy('created_at', 'asc')
                ->get();
    
                foreach ($messages as $message) {
                    if (!$message->is_read && $message->receiver_id == $currentUserId) {
                        $message->is_read = true;
                        $message->save();
    
                        Notification::where('user_id', $currentUserId)
                            ->where('type', 'New Message')
                            ->where('read', 0)
                            ->where('message_id', $message->id)
                            ->update(['read' => 1]);
                    }
                }
            }
        }
    
        $sentMessagesUserIds = Message::where('sender_id', $currentUserId)->pluck('receiver_id');
        $receivedMessagesUserIds = Message::where('receiver_id', $currentUserId)->pluck('sender_id');
    
        $messagedUserIds = $sentMessagesUserIds->merge($receivedMessagesUserIds)->unique()->filter(function ($id) use ($currentUserId) {
            return $id != $currentUserId;
        });
    
        // Fetch users with institution details and message data
        $allUsers = User::whereIn('id', $messagedUserIds)->with('institutionDetails')->get()->map(function ($user) use ($currentUserId) {
            $user->lastMessage = Message::where(function ($query) use ($user, $currentUserId) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->where(function ($query) use ($currentUserId) {
                $query->where('sender_id', $currentUserId)
                      ->orWhere('receiver_id', $currentUserId);
            })
            ->orderByDesc('created_at')
            ->first();
            
            $user->unreadMessageCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->count();
    
            $user->backgroundClass = $user->unreadMessageCount > 0 ? 'bg-dark text-white' : '';
    
            return $user;
        })->sortByDesc(function ($user) {
            return optional($user->lastMessage)->created_at;
        });
    
        if ($recipient && !$allUsers->contains($recipient)) {
            $recipient->lastMessage = null;
            $recipient->unreadMessageCount = 0;
            $recipient->backgroundClass = '';
            $allUsers->prepend($recipient);
        }
    
        return view('messages.index', compact('user', 'allUsers', 'recipient', 'messages'));
    }
    
    

public function store(Request $request)
{
    $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'content' => 'required|string',
    ]);

    $message = Message::create([
        'sender_id' => Auth::id(),
        'receiver_id' => $request->receiver_id,
        'content' => $request->content,
    ]);

    // Create a notification for the message receiver
    $sender = Auth::user();
    Notification::create([
        'user_id' => $request->receiver_id,
        'sender_id' => $sender->id,
        'type' => 'New Message',
        'message_id' => $message->id,
        'admin_message_id' => null,
        'follower_name' => $sender->first_name . ' ' . $sender->surname,
        'message' => 'sent you a message.',
    ]);

    $recipient = User::findOrFail($request->receiver_id);

    // Send email notification to the recipient
    Mail::to($recipient->email)->send(new MessageReceived($sender, $message->content, $recipient));

    return redirect()->route('messages.index', ['username' => $recipient->username])->with('success', 'Message sent successfully.');
}
    
}
