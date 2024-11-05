<?php

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PostRepostedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $reposter;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Post $post, $reposter)
    {
        $this->post = $post;
        $this->reposter = $reposter;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Post has been Reposted!')
                    ->view('emails.post_reposted')
                    ->with([
                        'post' => $this->post,
                        'reposter' => $this->reposter,
                    ]);
    }
}
