<?php

namespace App\Domains\Job\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewJobCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @var \App\Domains\Auth\Models\User */
    protected $user;

    /** @var \App\Domains\Job\Models\Job */
    protected $job;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * @param \App\Domains\Auth\Models\User $user
     * @param \App\Domains\Job\Models\Job $job
     */
    public function __construct($user, $job)
    {
        $this->user = $user;
        $this->job = $job;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('[Automatorr] - New job posted - ' . $this->job->name))
            ->to($this->user->email, $this->user->name)
            ->view('mails.new-job-created')
            ->with([
                'user' => $this->user,
                'jobName' => $this->job->name,
                'jobUrl' => route('frontend.freelancer.index', ['highlight_job' => $this->job->id])
            ]);
    }
}
