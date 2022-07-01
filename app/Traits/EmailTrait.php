<?php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SendSingleEmail;
use App\Models\EmailCredentials;

trait EmailTrait {

    /**
     * Creates single email send method
     *
     * @param string $subject - Email Subject
     * @param string $recipientEmail
     * @param array $data
     * @param string $template - Email blade template
     * 
     * @return array
     */
    public function sendSingleEmail($subject, $recipientEmail, $data, $template) {

        $emailConfig = EmailCredentials::firstOrFail();
        setEmailCredentials($emailConfig);

        try {
            Mail::to($recipientEmail)->send(new SendSingleEmail($subject, $data, $template));

            return true;
        } catch (\Throwable $th) {
            Log::error($th);

            return false;
        }

    }
}
