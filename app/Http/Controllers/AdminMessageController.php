<?php

namespace App\Http\Controllers;

use App\Mail\AdminMessageNotification as MailAdminMessageNotification;
use App\Models\Admin;
use App\Models\AdminMessage;
use App\Models\AdminReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Mail\AdminMessageNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserReplyNotification;
use App\Mail\AdminReplyNotification;

class AdminMessageController extends Controller
{
   
    public function index($messageId = null)
    {
        $user = Auth::user();
        $messages = AdminMessage::where('user_id', $user->id)->latest()->get();
        $currentMessage = $messageId ? AdminMessage::find($messageId) : null;

        // Get the current message based on messageId
        $currentMessage = $messageId ? AdminMessage::find($messageId) : null;
        
        // If a specific message is selected, mark its unread replies as "read"
        if ($currentMessage) {
            // Find all unread replies for the current message by the user and mark them as read
            AdminReply::where('message_id', $currentMessage->id)
                ->where('user_id', $user->id)
                ->where('is_read', false)
                ->where('admin_id', 2)
                ->update(['is_read' => true]);

                 // Mark the corresponding notification as read
                 Notification::where('user_id', $user->id)
                 ->where('read', false)
                 ->where('admin_message_id', $currentMessage->id)
                 ->update(['read' => true]);
        }

        $unreadMessageReplies = AdminReply::where('user_id', $user->id)
        ->where('is_read', false)
        ->where('admin_id', 2)
        ->latest()->get();
        
        $unreadMessagerepliesCount = $unreadMessageReplies->count();  

    
        return view('admin-messages', compact('user', 'messages', 'currentMessage', 'unreadMessagerepliesCount'));
    }
    

// Store the message in the database
public function store(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    $userId = Auth::id();
    $user = Auth::user();
    $adminId = 2; 

    $admin = Admin::find($adminId);
    $adminEmail = $admin ? $admin->email : null;

   // Create a new message in the database
    $adminMessage = AdminMessage::create([
        'user_id' => $userId,
        'sender_id' => $userId,
        'admin_id' => $adminId,
        'receiver_id' => $adminId,
        'subject' => $request->subject,
        'message' => $request->message,
        'is_read' => false, // Default value for read status
    ]);

     // Send an email notification to the admin
     Mail::to($adminEmail)->send(new AdminMessageNotification($adminMessage, $user));

    // Redirect back with a success message
    Alert::success('Your message has been sent successfully, we will respond to you shortly.');
    return redirect()->route('admin-messages.index'); 
}

    // Admin can view all messages
    public function adminMessages()
    {
        
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
        $message->totalUnreadCount = ($initialUnread ? 1 : 0) + $unreadRepliesCount;
    });   
    
       // Calculate the unread count for the initial message and its replies - for the sidebar list
       $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
       $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
       $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;

        return view('admin-panel.messages', compact('initialMessages', 'totalUnreadCount'));
    }

    public function show($id) {

        $initialMessages = AdminMessage::with('user') 
        ->orderBy('created_at', 'desc') 
        ->get();
        
        
        $message = AdminMessage::with('replies')
            ->where('id', $id)
            ->firstOrFail();
            
    
    if (!$message->is_read) {
        $message->is_read = true;
        $message->save(); // Save the change to the database
    }

    // Mark unread replies (where admin_id is null) as read
    $unreadReplies = $message->replies()->where('admin_id', null)->where('is_read', false)->get();

    foreach ($unreadReplies as $reply) {
        $reply->is_read = true;
        $reply->save(); // Save each reply as read
    } 
            
        // Calculate the unread count for the initial message and its replies - for the sidebar list
        $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
        $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
        $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;   
   
        return view('admin-panel.show', compact('message', 'initialMessages', 'totalUnreadCount'));
    }


    public function storeUserReply(Request $request, $messageId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        

        $reply =  AdminReply::create([
            'message_id' => $messageId,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_read' => false,
        ]);

        
        // Get the initial message details
        $initialMessage = AdminMessage::findOrFail($messageId);

        $adminId = 2; 
        $admin = Admin::find($adminId);
        $adminEmail = $admin ? $admin->email : null;
        $messageId = $initialMessage->id;

        // Send an email notification to the admin
        Mail::to($adminEmail)->send(new UserReplyNotification($reply, $initialMessage, $messageId));


        return redirect()->route('admin-messages.index', $messageId)->with('success', 'Reply sent successfully');
    }

    public function storeAdminReply(Request $request, $messageId)
{
    // Validate the request
    $request->validate([
        'message' => 'required|string',
    ]);

    // Find the original message
    $message = AdminMessage::findOrFail($messageId);
    

    // Create a new reply
    $reply = new AdminReply();
    $reply->message_id = $message->id;
    $reply->user_id = $message->user_id; 
    $reply->admin_id = 2;
    $reply->message = $request->message;
    $reply->is_read = false;
    $reply->save();

    // Create new notification for user

    $adminId = 2; 

    Notification::create([
        'user_id' => $message->user_id,
        'sender_id' => $adminId,
        'admin_message_id' => $message->id,
        'type' => 'New Admin Message',
        'follower_name' => null,
        'message' => 'admin sent you a message.',
    ]);
   

    $userId = $message->user_id; 
    $user = User::find($userId);
    $userEmail = $user->email;
    $userName = $user->first_name . ' ' . $user->surname;
    $messageSubject = $message->subject;

    Mail::to($userEmail)->send(new AdminReplyNotification($userName, $messageSubject, $reply->message, $message->id));
    

    // Optionally, you can add a success message or redirect to a different page
    return redirect()->back()->with('success', 'Reply sent successfully!');
}



}
