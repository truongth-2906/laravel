<?php

namespace App\Domains\Job\Listeners;

use App\Domains\Job\Mail\NewJobCreated as MailNewJobCreated;
use App\Domains\Auth\Services\FreelancerService;
use App\Domains\Job\Events\NewJobCreated;
use Illuminate\Support\Facades\Mail;

/**
 * Class JobListener.
 */
class JobListener
{
    /** @var FreelancerService */
    protected $freelancerService;

    /**
     * @param FreelancerService $freelancerService
     */
    public function __construct(FreelancerService $freelancerService)
    {
        $this->freelancerService = $freelancerService;
    }

    /**
     * @param $event
     */
    public function onNewJobCreated(NewJobCreated $event)
    {
        $freelancers = $this->freelancerService->getUserNotHidden();
        foreach ($freelancers as $freelancer) {
            Mail::queue((new MailNewJobCreated($freelancer, $event->job))->onQueue('mails'));
        }
        $event->job->update(['has_sended_mail' => true]);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            NewJobCreated::class,
            'App\Domains\Job\Listeners\JobListener@onNewJobCreated'
        );
    }
}
