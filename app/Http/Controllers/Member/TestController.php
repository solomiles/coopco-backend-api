<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\EmailCredentials;
use App\Traits\EmailTrait;

class TestController extends Controller
{
    use EmailTrait;

    public function test()
    {
        $subject = 'Fine Wine';
        $recipientData = ['spinmind@thespamfather.com' => 'Hello, welcome to PHP', 'gaveprove@pizzajunk.com' => 'Hello, welcome to Javascript', 'medicinemaster@pizzajunk.com' => 'Hello, welcome to Javascript', 'northfour@chewydonut.com' => 'Welcome to heaven.'];
        $template = 'bulk-test';

        try {
            $emailCredentials = EmailCredentials::firstOrFail();
            setEmailCredentials($emailCredentials);

            if ($this->sendBulkEmail($subject, $recipientData, $template)) {
                echo "Done";
            } else {
                echo "Failed";
            }
        } catch (\Throwable$th) {
            throw $th;
        }
    }
}
