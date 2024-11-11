<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClassRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $liveClass;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($liveClass, $user)
    {
        $this->liveClass = $liveClass;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Class Registration')
                    ->view('emails.class-registration-notification')
                    ->with([
                        'liveClass' => $this->liveClass,
                        'user' => $this->user,
                    ]);
    }
}
