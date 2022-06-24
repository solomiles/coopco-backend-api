<?php

namespace App\Traits;

use App\Jobs\SendEmail;
use App\Mail\SendBulkEmail;
use App\Mail\SendSingleEmail;
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
     * @param string $subject
     * @param array $recipientData
     * @param string $template
     *
     * @return array
     */
    public function sendBulkEmail($subject, $recipientData, $template)
    {
        try {
            $details = ['subject' => $subject, 'recipient_data' => $recipientData, 'template' => $template];
            SendEmail::dispatch($details);

            return true;
        } catch (\Throwable$th) {
            Log::error($th);

            return false;
        }
    }
}
