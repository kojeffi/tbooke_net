<?php

namespace App\Mail;

use App\Models\AdminMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $adminMessage;
    public $user;
    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($adminMessage, $user)
    {
        $this->adminMessage = $adminMessage;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Message from User')
                    ->view('emails.admin-message-notification');
    }
}

