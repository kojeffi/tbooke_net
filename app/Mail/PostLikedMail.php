<?php

namespace App\Mail;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PostLikedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $liker;

    public function __construct(Post $post, User $liker)
    {
        $this->post = $post;
        $this->liker = $liker;
    }

    public function build()
    {
        return $this->subject('Your Post Was Liked!')
                    ->view('emails.post_liked');
    }
}
