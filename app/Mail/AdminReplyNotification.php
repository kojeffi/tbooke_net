<?php

namespace App\Mail;

use App\Models\AdminReply;
use App\Models\AdminMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $messageSubject;
    public $adminReply;
    public $messageId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName, $messageSubject, $adminReply, $messageId)
    {
        $this->userName = $userName;
        $this->messageSubject = $messageSubject;
        $this->adminReply = $adminReply;
        $this->messageId = $messageId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Reply from Admin')
                    ->view('emails.admin-reply-notification');
    }
}
