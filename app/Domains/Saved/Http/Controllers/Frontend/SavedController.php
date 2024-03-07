<?php

namespace App\Domains\Saved\Http\Controllers\Frontend;

use App\Domains\Auth\Services\FreelancerService;
use App\Domains\Job\Services\JobService;
use App\Domains\Review\Services\ReviewService;
use App\Domains\Saved\Services\SavedService;
use App\Exceptions\GeneralException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 *
 */
class SavedController
{
    /** @var JobService */
    protected JobService $jobService;

    /** @var ReviewService */
    protected ReviewService $reviewService;

    /**
     * @var SavedService
     */
    protected SavedService $savedService;

    /**
     * @var FreelancerService
     */
    protected FreelancerService $freelancerService;

    /**
     * @param JobService $jobService
     * @param ReviewService $reviewService
     * @param FreelancerService $freelancerService
     * @param SavedService $savedService
     */
    public function __construct(
        JobService        $jobService,
        ReviewService     $reviewService,
        SavedService      $savedService,
        FreelancerService $freelancerService
    )
    {
        $this->jobService = $jobService;
        $this->reviewService = $reviewService;
        $this->savedService = $savedService;
        $this->freelancerService = $freelancerService;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function listJobs(Request $request)
    {
        $orderBy = $request->query('orderBy', null);

        if ($request->wantsJson()) {
            $jobs = $this->jobService->listJobsSaved($request, config('paging.quantity'), true);

            return response()->json([
                'html' => view('frontend.saved.job.table', compact('jobs', 'orderBy'))->render(),
                'current_page' => $jobs->lastPage()
            ], Response::HTTP_OK);
        }

        $jobs = $this->jobService->listJobsSaved($request);

        return view('frontend.saved.job.index', compact('jobs', 'orderBy'));
    }

    /**
     * @param Request $request
     * @return View|Factory|Response
     */
    public function listFreelancers(Request $request)
    {
        $orderBy = $request->query('orderBy', null);

        if ($request->wantsJson()) {
            $freelancers = $this->freelancerService->listFreelancerSaved($request, config('paging.quantity'), true);
            return response()->json([
                'html' => view('frontend.saved.freelancer.table', compact('freelancers', 'orderBy'))->render(),
                'current_page' => $freelancers->lastPage()
            ], Response::HTTP_OK);
        }
        $freelancers = $this->freelancerService->listFreelancerSaved($request);

        return view('frontend.saved.freelancer.index', compact('freelancers', 'orderBy'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse|RedirectResponse
     */
    public function jobPreview(Request $request, $id)
    {
        if (request()->wantsJson()) {
            try {
                $job = $this->jobService->findJobSaved($id, $request);
                $previousJob = $this->jobService->getPreviewJobSaved(PREVIOUS, $id, $request);
                $nextJob = $this->jobService->getPreviewJobSaved(NEXT, $id, $request);
                $myReview = $this->reviewService->getMyReviewJob(auth()->id(), $job->id, JOB_REVIEW);
                $jobReviews = $this->reviewService->getReviewJobByJobId($job->id);

                return response()->json([
                    'html' => view('frontend.saved.job.preview', compact('job', 'previousJob', 'nextJob', 'myReview', 'jobReviews'))->render(),
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

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function freelancerPreview(Request $request, $id)
    {
        if (request()->wantsJson()) {
            try {
                $freelancer = $this->freelancerService->findFreelancerSaved($id, $request);
                $previousFreelancer = $this->freelancerService->getPreviewSaved(PREVIOUS, $id, $request);
                $nextFreelancer = $this->freelancerService->getPreviewSaved(NEXT, $id, $request);
                $myReview = $this->reviewService->getMyReviewJob(auth()->id(), $id, USER_REVIEW);
                $freelancerReviews = $this->reviewService->getReviewByUserId($id);

                return response()->json([
                    'html' => view('frontend.saved.freelancer.preview', compact('freelancer', 'previousFreelancer', 'nextFreelancer', 'myReview', 'freelancerReviews'))->render(),
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

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GeneralException
     * @throws Throwable
     */
    public function saveJob(Request $request)
    {
        $saved = $this->savedService->saveJob($request->all(), $this->jobService);
        if ($saved) {
            return response()->json([
                'message' => __('Saved job success.')
            ], Response::HTTP_OK);
        }
        return response()->json([
            'message' => __('Saved job failed.'),
            'error' => true,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GeneralException
     * @throws Throwable
     */
    public function saveFreelancer(Request $request)
    {
        $saved = $this->savedService->saveFreelancer($request->all(), $this->freelancerService);
        if ($saved) {
            return response()->json([
                'message' => __('Saved freelancer success.')
            ], Response::HTTP_OK);
        }
        return response()->json([
            'message' => __('Saved freelancer failed.'),
            'error' => true,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
