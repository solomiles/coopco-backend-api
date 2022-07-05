<?php

namespace App\Traits;

use App\Jobs\SendEmail;
use App\Mail\SendSingleEmail;
use App\Models\EmailCredentials;
use App\Models\Cooperative;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait EmailTrait
{

    /**
     * Creates single email send method
     *
     * @param string $subject - Email Subject
     * @param string $recipientEmail
     * @param array $data
     * @param string $template - Email blade template
     *
     * @return boolean
     */
    public function sendSingleEmail($subject, $recipientEmail, $data, $template)
    {

        $emailConfig = EmailCredentials::firstOrFail();
        setEmailCredentials($emailConfig);

        try {
            Mail::to($recipientEmail)->send(new SendSingleEmail($subject, $data, $template));

            return true;
        } catch (\Throwable$th) {
            Log::error($th);

            return false;
        }

    }

    /**
     * Send bulk email
     *
     * @param string $subject - Email Subject
     * @param array $recipientData - [email=>[data], ...]
     * @param string $template - Email blade template
     *
     * @return array
     */
    public function sendBulkEmail($subject, $recipientData, $template)
    {
        $emailConfig = EmailCredentials::firstOrFail();
        setEmailCredentials($emailConfig);
        
        try {
            $details = ['subject' => $subject, 'recipient_data' => $recipientData, 'template' => $template];
            SendEmail::dispatch($details);

            return true;
        } catch (\Throwable$th) {
            Log::error($th);

            return false;
        }
    }

    /**
     * Send feedback email to admin when an error occurs when sending emails
     *
     * @param string $failedMailsubject - Email Subject for attempted email
     * @param array $data
     *
     * @return boolean - true on success | false on failure
     */
    public function feedBackEmail($failedMailsubject, $emails)
    {
        $subject = 'Error sending emails!';
        $template = 'failed-emails';
        $cooperative = (Cooperative::firstOrFail())->name;
        $data = ['emails' => $emails, 'cooperative' => $cooperative, 'subject' => $failedMailsubject];

        if ($this->sendSingleEmail($subject, env('FAILED_EMAIL_ADDRESS'), $data, $template)) {
            return true;
        } else {
            return false;
        }
    }
}
