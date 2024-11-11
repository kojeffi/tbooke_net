<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FollowNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $follower;
    public $followedUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($follower, $followedUser)
    {
        $this->follower = $follower;
        $this->followedUser = $followedUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Connection: You have a new follower')
                    ->view('emails.follow-notification');
    }
}
