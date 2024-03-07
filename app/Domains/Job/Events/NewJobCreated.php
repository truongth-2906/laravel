<?php

namespace App\Domains\Job\Events;

use App\Domains\Job\Models\Job;
use Illuminate\Queue\SerializesModels;

/**
 * Class NewJobCreated.
 */
class NewJobCreated
{
    use SerializesModels;

    /**
     * @var Job
     */
    public $job;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }
}
