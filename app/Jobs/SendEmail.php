<?php

namespace App\Jobs;

use App\Mail\SendBulkEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->details['recipient_data'] as $email => $data) {
            try {
                $mail = new SendBulkEmail($this->details['subject'], $data, $this->details['template']);
                Mail::to($email)->send($mail);

                return true; // Modify payload here to remove email address that has been serviced
            } catch (\Throwable$th) {
                Log::error($th);

                return false; // Retry after failure
            }
        }
    }
}
