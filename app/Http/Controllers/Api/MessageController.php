<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\MessageReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Display a listing of messages for a specific user.
     *
     * @param string|null $username
     * @return JsonResponse
     */
    public function index($username = null): JsonResponse
    {
        try {
            $currentUserId = Auth::id();
            $recipient = null;
            $messages = collect();

            // If a specific username is provided, load the conversation with that user
            if ($username) {
                $recipient = User::where('username', $username)->first();

                if ($recipient) {
                    // Fetch messages exchanged with the recipient
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

                    // Mark messages as read
                    foreach ($messages as $message) {
                        if (!$message->is_read && $message->receiver_id == $currentUserId) {
                            $message->is_read = true;
                            $message->save();

                            // Mark the corresponding notification as read
                            Notification::where('user_id', $currentUserId)
                                ->where('type', 'New Message')
                                ->where('read', 0)
                                ->where('message_id', $message->id)
                                ->update(['read' => 1]);
                        }
                    }
                }
            }

            // Get user IDs of people the current user has exchanged messages with
            $sentMessagesUserIds = Message::where('sender_id', $currentUserId)->pluck('receiver_id');
            $receivedMessagesUserIds = Message::where('receiver_id', $currentUserId)->pluck('sender_id');

            $messagedUserIds = $sentMessagesUserIds->merge($receivedMessagesUserIds)->unique()->filter(function ($id) use ($currentUserId) {
                return $id != $currentUserId; // Exclude the current user's own ID
            });

            // Fetch users and include last message, unread message count, and styling
            $allUsers = User::whereIn('id', $messagedUserIds)->get()->map(function ($user) use ($currentUserId) {
                // Get the last message exchanged with the user
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

                // Count unread messages from this user to the current user
                $user->unreadMessageCount = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $currentUserId)
                    ->where('is_read', false)
                    ->count();

                // Determine the background class based on unread messages
                $user->backgroundClass = $user->unreadMessageCount > 0 ? 'bg-dark text-white' : '';

                return $user;
            })->sortByDesc(function ($user) {
                return $user->lastMessage->created_at; // Sort by the latest message
            });

            return response()->json([
                'user' => Auth::user(),
                'allUsers' => $allUsers,
                'recipient' => $recipient,
                'messages' => $messages,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching messages: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch messages'], 500);
        }
    }

    /**
     * Store a newly created message.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        try {
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

            return response()->json(['message' => 'Message sent successfully'], 201);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }
}
