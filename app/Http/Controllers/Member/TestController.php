<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Traits\EmailTrait;

class TestController extends Controller
{
    use EmailTrait;

    public function test()
    {
        $subject = 'Fine Wine';
        $recipientData = ['wisdomntui@gmail.com' => 'Hello, welcome to PHP', 'endyalfred7gmail.com' => 'Hello bae', 'wisdom.ntui@coopco.com.ng' => 'Welcome to coopco!'];
        $template = 'bulk-test';

        try {
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
