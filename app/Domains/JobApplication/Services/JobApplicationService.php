<?php

namespace App\Domains\JobApplication\Services;

use App\Domains\Job\Models\Job;
use App\Domains\JobApplication\Models\JobApplication;
use App\Services\BaseService;
use App\Domains\Job\Services\JobService;
use App\Domains\Notification\Models\Notification;
use App\Domains\Notification\Services\NotificationService;
use App\Domains\Transaction\Services\TransactionService;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class JobApplicationService.
 */
class JobApplicationService extends BaseService
{
    /** @var NotificationService */
    protected $notificationService;

    /**
     * @var JobService
     */
    protected JobService $jobService;

    /** @var TransactionService */
    protected $transactionService;

    /**
     * @param JobApplication $jobApplication
     * @param JobService $jobService
     * @param NotificationService $notificationService
     * @param TransactionService $transactionService
     */
    public function __construct(
        JobApplication $jobApplication,
        JobService $jobService,
        NotificationService $notificationService,
        TransactionService $transactionService
    ) {
        $this->model = $jobApplication;
        $this->jobService = $jobService;
        $this->notificationService = $notificationService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param Request $request
     * @param Job $job
     * @return bool|string
     */
    public function updateStatus(Request $request, Job $job)
    {
        DB::beginTransaction();
        try {
            if (!$job) {
                return false;
            }

            if ($request->status == JobApplication::STATUS_DONE) {
                $this->markDone($request, $job);
            } else {
                $result = $this->updateStatusIfNotSet($request, $job);
            }
            DB::commit();

            return isset($result) ? $result : true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getByUserIdAndJobId($params)
    {
        return $this->model->where([
            ['user_id', $params->userId], ['job_id', $params->jobId]
        ])->first();
    }

    /**
     * @param $job
     * @return bool
     */
    public function checkMarkDoneJob($job): bool
    {
        foreach ($job->applicants as $jobApply) {
            if ($jobApply->application->status == JobApplication::STATUS_APPROVE) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $request
     * @param $paging
     * @return mixed
     */
    public function getJobDoneByUserId($request, $paging)
    {
        return $this->model->select([
            'job_applications.id as id',
            'job_applications.job_id as job_id'
        ])->where([
            ['job_applications.user_id', auth()->id()],
            ['job_applications.status', JobApplication::STATUS_DONE]
        ])->join('jobs', function ($query) {
            $query->on('jobs.id', '=', 'job_applications.job_id');
        })->join('users', function ($query) {
            $query->on('users.id', '=', 'job_applications.user_id');
        })->when($request->key_search, function ($query) use ($request) {
            $query->where('jobs.name', 'like', '%' . $request->key_search . '%');
        })->when($request->sector_id, function ($query) use ($request) {
            $query->join('sectors', function ($query) {
                $query->on('sectors.id', '=', 'users.sector_id');
            })->where('sectors.id', $request->sector_id);
        })->when($request->category_id, function ($query) use ($request) {
            $query->join('category_job', function ($query) {
                $query->on('category_job.job_id', '=', 'jobs.id');
            })->join('categories', function ($query) {
                $query->on('categories.id', '=', 'category_job.category_id');
            })->whereIn('categories.id', $request->category_id);
        })->when($request->experience_id, function ($query) use ($request) {
            $query->join('experiences', function ($query) {
                $query->on('experiences.id', '=', 'jobs.experience_id');
            })->where('experiences.id', $request->experience_id);
        })->when($request->country_id, function ($query) use ($request) {
            $query->join('countries', function ($query) {
                $query->on('countries.id', '=', 'jobs.country_id');
            })->where('countries.id', $request->country_id);
        })->groupBy('id')
            ->paginate($paging);
    }

    /**
     * @param Request $request
     * @param Job $job
     * @return bool
     */
    public function markDone(Request $request, Job $job)
    {
        $jobApplication = $this->model->where([
            ['job_id', $request->jobId], ['status', JobApplication::STATUS_APPROVE]
        ]);
        $this->jobService->markDoneJob($job->id);
        $jobApplication->update(['status' => JobApplication::STATUS_DONE]);

        $job->load([
            'user',
            'applicants' => function ($e) {
                $e->where('status', JobApplication::STATUS_DONE);
            }
        ]);
        $this->cancelDeposited($job);
        $this->notificationService->createByJob($job, Notification::JOB_DONE_TYPE);

        return true;
    }

    /**
     * @param Job $jobId
     * @return void
     */
    public function cancelDeposited(Job $job)
    {
        $query = $this->model->where([
            'job_id' => $job->id,
            'status' => JobApplication::STATUS_ESCROW_HANDLING
        ]);
        $jobApplicationsEscrowHandling = $query->pluck('user_id')->toArray();
        $query->update(['status' => JobApplication::STATUS_PENDING]);

        $this->transactionService->cancelTransactionWhenJobDone($job->id, ...$jobApplicationsEscrowHandling);
    }

    /**
     * @param Request $request
     * @param Job $job
     * @return void
     */
    public function updateStatusIfNotSet(Request $request, Job $job)
    {
        $jobApplication = $this->getByUserIdAndJobId($request);
        $status = [JobApplication::STATUS_REJECT, JobApplication::STATUS_APPROVE, JobApplication::STATUS_ESCROW_HANDLING];

        throw_if(
            in_array(optional($jobApplication)->status, $status),
            Exception::class,
            'The state has been set before.',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

        if ($request->status == JobApplication::STATUS_APPROVE) {
            $jobApplication->load('user:id,name,escrow_email');
            $job->load('user:id,escrow_email');
            throw_if(
                !$redirectUrl = $this->transactionService->createWhenApprovedFreelancer($job, $jobApplication->user),
                Exception::class,
                'Create transaction failed.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            $request->replace(['status' => JobApplication::STATUS_ESCROW_HANDLING]);
        }
        $jobApplication->update(['status' => $request->status]);

        return isset($redirectUrl) ? $redirectUrl : true;
    }

    /**
     * @param int $jobId
     * @param int $userId
     * @param int|string $status
     * @return mixed
     */
    public function updateStatusByJobAndUser(int $jobId, int $userId, $status)
    {
        return $this->model->newQuery()->where([
            'job_id' => $jobId,
            'user_id' => $userId,
        ])->update([
            'status' => $status
        ]);
    }
}
