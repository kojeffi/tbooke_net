<?php

namespace App\Mail;

use App\Models\AdminReply;
use App\Models\AdminMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $reply;
    public $initialMessage;
    public $messageId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AdminReply $reply, AdminMessage $initialMessage, $messageId)
    {
        $this->reply = $reply;
        $this->initialMessage = $initialMessage;
        $this->messageId = $messageId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Reply from User')
                    ->view('emails.user-reply-notification');
    }
}
