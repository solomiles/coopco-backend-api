<?php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SendSingleEmail;

trait EmailTrait {

    /**
     * Creates single email send method
     *
     * @param array $data
     * @param string $recipientEmail
     * @return json
     */

    public function sendSingleEmail($recipientEmail, $data) {

        try {
            Mail::to($recipientEmail)->send(new SendSingleEmail($data));

            return response()->json([
                'status' => true,
                'message' => 'Email Sent'
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th);

            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }

    }
}
