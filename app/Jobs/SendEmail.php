<?php

namespace App\Jobs;

use App\Models\EmailCredentials;
use App\Traits\EmailTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EmailTrait;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    protected $details;

    /**
     * Create a new job instance.
     *
     * @param array $details - An array that holds recipient_data, email subject and email template
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
        // Set email credentials for cooperative
        $emailCredentials = EmailCredentials::firstOrFail();
        setEmailCredentials($emailCredentials);

        $recipientData = $this->details['recipient_data'];
        $failedMailData = [];

        foreach ($recipientData as $email => $data) {
            if (!$this->sendSingleEmail($this->details['subject'], $email, $data, $this->details['template'])) {
                $failedMailData[$email] = $data;
            }
        }

        // Check if there are failed mails in order to requeue or fail job manually
        if (!empty($failedMailData)) {
            if ($this->attempts() > 2) {
                // Send error message to coopco admin
                $this->feedBackEmail($this->details['subject'], array_keys($failedMailData));

                // hard fail after 3 attempts
                $this->fail();
            }

            // Update details property
            $this->details['recipient_data'] = $failedMailData;

            // Trigger updating of job payload
            $this->update();

            // requeue this job to be executes
            // in 5 seconds from now
            $this->release(60);
            return;
        }
    }

    /**
     * Call update() to ensure the Job object has it's internal pointers updated
     * with the latest Job properties.
     */
    protected function update()
    {
        // get top level Job (\Illuminate\Queue\Jobs\Job)
        $job = $this->job;
        $refobject = new \ReflectionObject($job);

        // get it's job property (\stdClass, in this case it's Illuminate\Queue\Jobs\DatabaseJobRecord, unless we change the driver.)
        $jobrecord = $refobject->getProperty('job');
        $jobrecord->setAccessible(true);

        //
        // the below will only work as long as job is Illuminate\Queue\Jobs\DatabaseJobRecord
        // I.E our driver must be `database`.
        //

        // create a new reflection object of the next job property ($jobrecord)
        $record = $jobrecord->getValue($job);
        $refobject = new \ReflectionObject($record);

        // with our new reflection object, access the record property
        $recordproperty = $refobject->getProperty('record');
        $recordproperty->setAccessible(true);

        // get the record property and fetch the payload from it.
        $record = $recordproperty->getValue($record);
        $payload = $record->payload;

        $payload = \json_decode($payload, true);

        // keep job
        $job = $this->job;
        $this->job = null;

        // overwrite the serialized object with a new copy of it.
        $payload['data']['command'] = \serialize($this);

        // reset the job after serialization
        $this->job = $job;
        $record->payload = \json_encode($payload);
    }
}
