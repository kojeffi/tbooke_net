<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AdminMessageNotification;
use App\Mail\AdminReplyNotification;
use App\Mail\UserReplyNotification;
use App\Models\Admin;
use App\Models\AdminMessage;
use App\Models\AdminReply;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AdminMessageController extends Controller
{
    /**
     * Retrieve User Messages
     * GET /api/user/messages?messageId=optional
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $messageId = $request->query('messageId');

            $messages = AdminMessage::where('user_id', $user->id)->latest()->get();
            $currentMessage = $messageId ? AdminMessage::find($messageId) : null;

            // If a specific message is selected, mark its unread replies as "read"
            if ($currentMessage) {
                // Ensure the message belongs to the authenticated user
                if ($currentMessage->user_id !== $user->id) {
                    return response()->json([
                        'message' => 'Unauthorized access to this message.'
                    ], Response::HTTP_FORBIDDEN);
                }

                // Mark unread replies as read
                AdminReply::where('message_id', $currentMessage->id)
                    ->where('user_id', $user->id)
                    ->where('is_read', false)
                    ->where('admin_id', 2) // Assuming admin_id 2 is the admin
                    ->update(['is_read' => true]);

                // Mark corresponding notifications as read
                Notification::where('user_id', $user->id)
                    ->where('read', false)
                    ->where('admin_message_id', $currentMessage->id)
                    ->update(['read' => true]);
            }

            $unreadMessageReplies = AdminReply::where('user_id', $user->id)
                ->where('is_read', false)
                ->where('admin_id', 2)
                ->latest()
                ->get();

            $unreadMessageRepliesCount = $unreadMessageReplies->count();

            return response()->json([
                'data' => [
                    'user' => $user,
                    'messages' => $messages,
                    'current_message' => $currentMessage,
                    'unread_message_replies_count' => $unreadMessageRepliesCount,
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching user messages: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve messages.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a New Message from User
     * POST /api/user/messages
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate the incoming request
            $validatedData = $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $adminId = 2; // Assuming admin_id 2 is the admin

            $admin = Admin::find($adminId);
            if (!$admin) {
                return response()->json([
                    'message' => 'Admin not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            // Create a new message in the database
            $adminMessage = AdminMessage::create([
                'user_id' => $user->id,
                'sender_id' => $user->id,
                'admin_id' => $adminId,
                'receiver_id' => $adminId,
                'subject' => $validatedData['subject'],
                'message' => $validatedData['message'],
                'is_read' => false, // Default value for read status
            ]);

            // Send an email notification to the admin
            Mail::to($admin->email)->send(new AdminMessageNotification($adminMessage, $user));

            return response()->json([
                'message' => 'Your message has been sent successfully. We will respond to you shortly.',
                'data' => $adminMessage
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error storing user message: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send your message.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Admin Retrieves All Messages
     * GET /api/admin/messages
     */
    public function adminMessages()
    {
        try {
            $initialMessages = AdminMessage::with(['user', 'replies'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Iterate over the messages and compute unread counts
            $initialMessages->each(function ($message) {
                // Check if the initial message is unread
                $initialUnread = !$message->is_read;

                // Count unread replies from the user
                $unreadRepliesCount = $message->replies()
                    ->where('admin_id', null) // replies from the user
                    ->where('is_read', false)
                    ->count();

                // Calculate total unread count
                $message->total_unread_count = ($initialUnread ? 1 : 0) + $unreadRepliesCount;
            });

            // Calculate the unread count for the initial message and its replies - for the sidebar list
            $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
            $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
            $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;

            return response()->json([
                'data' => [
                    'initial_messages' => $initialMessages,
                    'total_unread_count' => $totalUnreadCount,
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching admin messages: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve admin messages.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show a Specific Message
     * GET /api/user/messages/{id}
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            $message = AdminMessage::with('replies')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Mark the message as read if not already
            if (!$message->is_read) {
                $message->is_read = true;
                $message->save();
            }

            // Mark unread replies (where admin_id is null) as read
            $unreadReplies = $message->replies()
                ->where('admin_id', null)
                ->where('is_read', false)
                ->get();

            foreach ($unreadReplies as $reply) {
                $reply->is_read = true;
                $reply->save();
            }

            // Calculate the unread count for the initial message and its replies - for the sidebar list
            $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
            $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
            $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;

            return response()->json([
                'data' => [
                    'message' => $message,
                    'total_unread_count' => $totalUnreadCount,
                ]
            ], Response::HTTP_OK);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Message not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Error showing message: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve the message.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a Reply from User
     * POST /api/user/messages/{id}/reply
     */
    public function storeUserReply(Request $request, $messageId)
    {
        try {
            $user = Auth::user();

            // Validate the request
            $validatedData = $request->validate([
                'message' => 'required|string',
            ]);

            // Find the original message
            $originalMessage = AdminMessage::where('id', $messageId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Create a new reply
            $reply = AdminReply::create([
                'message_id' => $messageId,
                'user_id' => $user->id,
                'message' => $validatedData['message'],
                'is_read' => false,
            ]);

            // Send an email notification to the admin
            $adminId = $originalMessage->admin_id; // Assuming admin_id is stored in the message
            $admin = Admin::find($adminId);
            if ($admin) {
                Mail::to($admin->email)->send(new UserReplyNotification($reply, $originalMessage, $messageId));
            }

            return response()->json([
                'message' => 'Reply sent successfully.',
                'data' => $reply
            ], Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Original message not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error storing user reply: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send reply.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a Reply from Admin
     * POST /api/admin/messages/{id}/reply
     */
    public function storeAdminReply(Request $request, $messageId)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'message' => 'required|string',
            ]);

            // Find the original message
            $originalMessage = AdminMessage::findOrFail($messageId);

            // Create a new reply
            $reply = AdminReply::create([
                'message_id' => $messageId,
                'user_id' => $originalMessage->user_id,
                'admin_id' => Auth::guard('admin-api')->id(),
                'message' => $validatedData['message'],
                'is_read' => false,
            ]);

            // Create a new notification for the user
            Notification::create([
                'user_id' => $originalMessage->user_id,
                'sender_id' => Auth::guard('admin-api')->id(),
                'admin_message_id' => $messageId,
                'type' => 'New Admin Message',
                'follower_name' => null,
                'message' => 'Admin sent you a message.',
            ]);

            // Send an email notification to the user
            $user = User::find($originalMessage->user_id);
            if ($user) {
                $userName = $user->first_name . ' ' . $user->surname;
                $messageSubject = $originalMessage->subject;
                Mail::to($user->email)->send(new AdminReplyNotification($userName, $messageSubject, $reply->message, $originalMessage->id));
            }

            return response()->json([
                'message' => 'Reply sent successfully.',
                'data' => $reply
            ], Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Original message not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error storing admin reply: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send admin reply.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
