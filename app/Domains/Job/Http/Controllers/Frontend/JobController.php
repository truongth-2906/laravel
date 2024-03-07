<?php

namespace App\Domains\Job\Http\Controllers\Frontend;

use App\Domains\Auth\Services\UserService;
use App\Domains\Job\Http\Requests\Frontend\CreateJobRequest;
use App\Domains\Job\Http\Requests\Frontend\UpdateRequest;
use App\Domains\Job\Models\Job;
use App\Domains\Job\Services\JobService;
use App\Domains\Review\Services\ReviewService;
use App\Exceptions\GeneralException;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 *
 */
class JobController
{
    /**
     * @var JobService
     */
    protected JobService $jobService;

    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @var ReviewService
     */
    protected ReviewService $reviewService;

    /**
     * @param JobService $jobService
     * @param UserService $userService
     * @param ReviewService $reviewService
     */
    public function __construct(
        JobService    $jobService,
        UserService   $userService,
        ReviewService $reviewService
    )
    {
        $this->jobService = $jobService;
        $this->userService = $userService;
        $this->reviewService = $reviewService;
    }

    /**
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $orderBy = $request->query('orderBy');
        $isCheckAll = (bool)$request->query('isCheckAll');
        $jobs = $this->jobService->all($orderBy);

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('frontend.job.manager-table', compact('jobs', 'orderBy', 'isCheckAll'))->render(),
            ], Response::HTTP_OK);
        }

        return view('frontend.job.manager', compact('jobs', 'orderBy', 'isCheckAll'));
    }

    /**
     * @return Application|Factory|View|RedirectResponse
     */
    public function create()
    {
        if (!Auth::user()->company_id){
            return redirect()->route('frontend.employer.setting')->with('warning', __('Please fully update the information before creating the job!'));
        }
        $previousUrl = $this->jobService->getPreviousUrl();
        $maxDescription = Job::MAX_DESCRIPTION;

        return view('frontend.job.create', compact('maxDescription', 'previousUrl'));
    }

    /**
     * @param CreateJobRequest $request
     * @return JsonResponse|RedirectResponse
     * @throws Throwable
     */
    public function store(CreateJobRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->jobService->createByEmployer($request);
            DB::commit();
            session()->flash('message', __('Job added successfully.'));

            if ($request->ajax()) {

                return response()->json([
                    'message' => __('Job added successfully.'),
                    'redirect' => route(EMPLOYER_INDEX)
                ], 200);
            }
            return redirect()->route(EMPLOYER_INDEX);
        } catch (Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => true
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', __('Job added failed.'));
        }
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function edit($id)
    {
        $job = $this->jobService->getJobWithUser($id);
        if ($job) {
            $maxDescription = Job::MAX_DESCRIPTION;

            return view('frontend.job.edit', compact('job', 'maxDescription'));
        }
        return redirect()->route(EMPLOYER_INDEX)->with('error', __("Can't edit work that doesn't belong to you"));
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function update(UpdateRequest $request, $id): RedirectResponse
    {
        $this->jobService->update($request->all(), $id);

        return redirect()->route(EMPLOYER_INDEX)->with('message', __('The job was successfully update.'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->jobService->deleteByEmployer($id);
            DB::commit();

            return redirect()->back()->with('message', __('Delete job successfully.'));
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', __('Delete job failed.'));
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|RedirectResponse
     */
    public function preview(Request $request, $id)
    {
        if ($request->wantsJson()) {
            try {
                $job = $this->jobService->findWithNotAuthor($id);
                $previousJob = $this->jobService->getPreviewJobNotAuthor(PREVIOUS, $id);
                $nextJob = $this->jobService->getPreviewJobNotAuthor(NEXT, $id);
                $myReview = $this->reviewService->getMyReviewJob(auth()->id(), $job->id, JOB_REVIEW);
                $jobReviews = $this->reviewService->getReviewJobByJobId($job->id);

                return response()->json([
                    'html' => view('frontend.job.preview', compact('job', 'previousJob', 'nextJob', 'myReview', 'jobReviews'))->render(),
                ], Response::HTTP_OK);
            } catch (Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => true,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return redirect()->back();
    }
}
