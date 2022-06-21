<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendSingleEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $data;

    public $template;

    /**
     * Create a new message instance.
     *
     * @param string $subject - Email Subject
     * @param array $data
     * @param string $template - Email blade template name
     * 
     * @return void
     */
    public function __construct($subject, $data, $template)
    {
        $this->subject = $subject;
        $this->template = $template;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return View
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->view('email.' . $this->template)
            ->with($this->data);
    }
}
