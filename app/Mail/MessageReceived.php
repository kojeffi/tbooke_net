<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class MessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $sender;
    public $recipient;
    public $messageContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $sender, $messageContent, $recipient)
    {
        $this->recipient = $recipient;
        $this->sender = $sender;
        $this->messageContent = $messageContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('You have a new message')
                    ->view('emails.message_received');
    }
}