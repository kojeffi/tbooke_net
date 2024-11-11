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

    public $user;
    public $messageSubject;
    public $adminReply;
    public $messageId;
    public $profileType;
    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $messageSubject, $adminReply, $messageId, $profileType)
    {
        $this->user = $user;
        $this->messageSubject = $messageSubject;
        $this->adminReply = $adminReply;
        $this->messageId = $messageId;
        $this->profileType = $profileType;
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
