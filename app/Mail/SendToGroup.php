<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\GroupPost;

class SendToGroup extends Mailable
{
    public $post;
    public $reposter;
    public $title;
    public $type;
    public $member;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(GroupPost $post, $reposter,$title,$type,$member)
    {
        $this->post = $post;
        $this->title = $title;
        $this->reposter = $reposter;
        $this->type = $type;
        $this->member = $member;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.send_group')
                    ->with([
                        'post' => $this->post,
                        'reposter' => $this->reposter,
                        'type' => $this->type,
                        'member' => $this->member,
                    ]);
    }
}

?>