<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBulkEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $data;

    public $template;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param array $data
     * @param string $template
     *
     * @return void
     */
    public function __construct($subject, $data, $template)
    {
        $this->subject = $subject;
        $this->data = $data;
        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->view('email.' . $this->template)
            ->with($this->data);
    }
}
