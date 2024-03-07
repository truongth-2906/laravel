<?php

namespace App\Domains\Transaction\Listeners;

use App\Domains\JobApplication\Services\JobApplicationService;
use App\Domains\Transaction\Events\StatusUpdated;
use App\Domains\Transaction\Events\UserHidden;
use App\Domains\Transaction\Services\TransactionService;

/**
 * Class TransactionListener.
 */
class TransactionListener
{
    /** @var JobApplicationService */
    protected $jobApplicationService;

    /** @var TransactionService */
    protected $transactionService;

    /**
     * @param JobApplicationService $jobApplicationService
     * @param TransactionService $transactionService
     */
    public function __construct(JobApplicationService $jobApplicationService, TransactionService $transactionService)
    {
        $this->jobApplicationService = $jobApplicationService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param $event
     */
    public function onStatusUpdated(StatusUpdated $event)
    {
        $this->jobApplicationService->updateStatusByJobAndUser(
            $event->transaction->job_id,
            $event->transaction->receiver_id,
            $event->status
        );
    }

    /**
     * @param UserHidden $event
     */
    public function onUserHidden(UserHidden $event)
    {
        $this->transactionService->cancelTransactionWhenUserHidden($event->user);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            StatusUpdated::class,
            'App\Domains\Transaction\Listeners\TransactionListener@onStatusUpdated'
        );

        $events->listen(
            UserHidden::class,
            'App\Domains\Transaction\Listeners\TransactionListener@onUserHidden'
        );
    }
}
