<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Freelancer;

use App\Domains\Auth\Http\Requests\Frontend\Freelancer\SettingAvailableRequest;
use App\Domains\Auth\Services\UserService;
use App\Domains\Job\Services\JobService;
use App\Domains\Saved\Services\SavedService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Domains\Auth\Http\Requests\Backend\Freelancer\UpdateFreelancerRequest;
use App\Domains\Review\Http\Requests\Frontend\StoreReviewRequest;
use App\Domains\Auth\Models\User;
use App\Domains\Review\Services\ReviewService;
use App\Exceptions\GeneralException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Domains\Portfolio\Services\PortfolioService;
use App\Domains\Auth\Services\FreelancerService;
use App\Domains\JobApplication\Services\JobApplicationService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Throwable;

class FreelancerController
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @var JobService
     */
    protected JobService $jobService;

    /**
     * @var PortfolioService
     */
    protected PortfolioService $portfolioService;

    /**
     * @var FreelancerService
     */
    protected FreelancerService $freelancerService;

    /**
     * @var ReviewService
     */
    protected ReviewService $reviewService;

    /**
     * @var JobApplicationService
     */
    protected JobApplicationService $jobApplicationService;

    /** @var SavedService */
    protected SavedService $savedService;

    /**
     * @param JobService $jobService
     * @param FreelancerService $freelancerService
     * @param PortfolioService $portfolioService
     * @param UserService $userService
     * @param ReviewService $reviewService
     * @param JobApplicationService $jobApplicationService
     */
    public function __construct(
        JobService            $jobService,
        FreelancerService     $freelancerService,
        PortfolioService      $portfolioService,
        UserService           $userService,
        ReviewService         $reviewService,
        JobApplicationService $jobApplicationService,
        SavedService          $savedService
    ) {
        $this->jobService = $jobService;
        $this->freelancerService = $freelancerService;
        $this->portfolioService = $portfolioService;
        $this->userService = $userService;
        $this->reviewService = $reviewService;
        $this->jobApplicationService = $jobApplicationService;
        $this->savedService = $savedService;
    }

    /**
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->query('highlight_job')) {
            return $this->detailJobHighlight($request);
        }
        $totalJobs = $this->jobService->count();
        $user = $this->userService->getUserInfo(Auth::user()->id);
        $jobs = $this->jobService->searchFreelancerJob($request, config('paging.quantity'), $user);

        $orderBy = $request->orderBy ?? TYPE_SORT_DESC;
        if ($request->ajax()) {
            $view = view('frontend.freelancer.table', compact('jobs', 'orderBy'))->render();
            return response()->json(['html' => $view]);
        }

        return view('frontend.user.dashboard', compact('totalJobs', 'jobs', 'orderBy'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function profile($id)
    {
        $freelancer = $this->freelancerService->getById($id);
        if ($freelancer && $freelancer->isFreelancer()) {
            return view('frontend.freelancer.profile', compact('freelancer'));
        }
        return redirect()->route(homeRoute())->with('error', __('The freelancer you requested could not be found.'));
    }

    /**
     * @return Application|Factory|View
     */
    public function setting()
    {
        $freelancer = auth()->user();
        $lengthBio = User::BIO_LENGTH;
        $categoryIds = $freelancer->categories->pluck('id')->toArray();

        return view('frontend.freelancer.setting', compact('freelancer', 'lengthBio', 'categoryIds'));
    }

    /**
     * @param UpdateFreelancerRequest $request
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function update(UpdateFreelancerRequest $request): RedirectResponse
    {
        if ($this->freelancerService->updateFreelancer($this->portfolioService, $request->all())) {
            return redirect()->route('frontend.freelancer.setting')->with('message', __('The freelancer was successfully updated.'));
        }
        return redirect()->back()->withInput()->with('error', __('The freelancer update failed.'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function jobApplication(Request $request)
    {
        $orderBy = strtoupper($request->query('orderBy')) == TYPE_SORT_ASC ?
            TYPE_SORT_ASC :
            TYPE_SORT_DESC;

        $jobs = $this->jobService->getMyJobApplied(auth()->id(), $request, config('paging.quantity'));

        if ($request->ajax()) {
            $view = view('frontend.freelancer.job-application-table', compact('jobs', 'orderBy'))->render();
            return response()->json([
                'html' => $view
            ], Response::HTTP_OK);
        }

        return view('frontend.freelancer.job-application', compact('jobs', 'orderBy'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function jobDetail($id)
    {
        $job = $this->jobService->getJobOpenById($id);
        if (is_null(auth()->user()->escrow_email)) {
            return redirect()->route('frontend.freelancer.payments.escrow_account.create', ['job_id' => $id])->with('warning', __('Please add escrow account before applying in job!'));
        }
        if (empty($job)) {
            return redirect()->route('frontend.freelancer.index');
        }
        return view('frontend.freelancer.job-detail', compact('job'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function jobPreview(Request $request): JsonResponse
    {
        $job = $this->jobService->getById($request->id);
        $user = $this->userService->getUserInfo(Auth::user()->id);
        $previousJob = $this->jobService->getJobPreview(PREVIOUS, $job, $user, $request);
        $nextJob = $this->jobService->getJobPreview(NEXT, $job, $user, $request);
        $myReview = $this->reviewService->getMyReviewJob(auth()->id(), $job->id, JOB_REVIEW);
        $jobReviews = $this->reviewService->getReviewJobByJobId($job->id);
        $jobApplied = $this->jobService->getAllMyJobApplied(auth()->id());
        $isFreelancer = true;
        $view = view(
            'frontend.freelancer.job-preview',
            compact('job', 'previousJob', 'nextJob', 'jobReviews', 'myReview', 'jobApplied', 'user', 'isFreelancer')
        )
            ->render();

        return response()->json(['html' => $view]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrentPageJob(Request $request): JsonResponse
    {
        $job = $this->jobService->getById($request->id);
        $user = $this->userService->getUserInfo(Auth::user()->id);
        $page = $this->jobService->getPage($job, $request, $user);

        return response()->json(['page' => $page]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function jobApply(Request $request): JsonResponse
    {
        try {
            $this->freelancerService->apply($request->all());

            return response()->json([
                'message' => __('Apply success.')
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => __('Apply failed.'),
                'error' => true,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse|void
     */
    public function jobApplicationPreview(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $job = $this->jobService->getJobApplicationById($id, auth()->id());

                $view = view(
                    'frontend.freelancer.job-application-preview'
                )->with([
                    'job' => $job,
                    'previousJob' => $this->jobService->getJobApplicationPreview(PREVIOUS, $id, auth()->id(), $request),
                    'nextJob' => $this->jobService->getJobApplicationPreview(NEXT, $id, auth()->id(), $request),
                    'myReview' => $this->reviewService->getMyReviewJob(auth()->id(), $job->id, JOB_REVIEW),
                    'jobReviews' => $this->reviewService->getReviewJobByJobId($job->id),
                ])
                    ->render();

                return response()->json(['html' => $view], Response::HTTP_OK);
            } catch (Exception $e) {
                return response()->json([
                    'html' => '',
                    'error' => true,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function available()
    {
        $freelancer = $this->freelancerService->getById(Auth::user()->id);
        return view('frontend.includes.setting-available', compact('freelancer'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function settingAvailable(SettingAvailableRequest $request): RedirectResponse
    {
        $this->freelancerService->settingAvailable($request->all());
        return redirect()->route('frontend.freelancer.available')->with('message', __('Setting available success!'));
    }

    /**
     * @param StoreReviewRequest $request
     * @return JsonResponse
     */
    public function addReviewJob(StoreReviewRequest $request)
    {
        $this->reviewService->createReviewJob($request);
        $reviews = $this->reviewService->getReviewJobByJobId($request->job_id);
        $view = view('frontend.freelancer.review', compact('reviews'))->render();

        return response()->json(['html' => $view]);
    }

    /**
     * @return Application|Factory|View|JsonResponse
     */
    public function jobSaved(Request $request)
    {
        $orderBy = $request->query('orderBy', null);
        $this->jobService->listJobsSaved($request);

        if ($request->wantsJson()) {
            $jobSaved = $this->jobService->listJobsSaved($request, config('paging.quantity'), true);
            return response()->json([
                'html' => view('frontend.freelancer.job-saved-table', compact('jobSaved', 'orderBy'))->render(),
                'current_page' => $jobSaved->lastPage()
            ], Response::HTTP_OK);
        }

        $jobSaved = $this->jobService->listJobsSaved($request);

        return view('frontend.freelancer.job-saved', compact('jobSaved', 'orderBy'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function jobDone(Request $request)
    {
        $jobDone = $this->jobService->getMyJobDone(auth()->id(), $request, config('paging.quantity'));
        if ($request->ajax()) {
            $view = view('frontend.freelancer.job-done-table', compact('jobDone'))->render();
            return response()->json(['html' => $view]);
        }
        return view('frontend.freelancer.job-done', compact('jobDone'));
    }

    /**
     * @return Application|Factory|View|JsonResponse
     */
    public function listPayment()
    {
        return view('frontend.freelancer.payment.index');
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse|RedirectResponse
     */
    public function jobDonePreview(Request $request, $id)
    {
        try {
            $job = $this->jobService->findJobDone($id, auth()->id());
            $with = [
                'job' => $job,
                'previousJob' => $this->jobService->getJobDonePreview(PREVIOUS, $id, auth()->id(), $request),
                'nextJob' => $this->jobService->getJobDonePreview(NEXT, $id, auth()->id(), $request),
                'myReview' => $this->reviewService->getMyReviewJob(auth()->id(), $job->id, JOB_REVIEW),
                'jobReviews' => $this->reviewService->getReviewJobByJobId($job->id),
            ];

            if ($request->ajax()) {
                $view = view(
                    'frontend.freelancer.job-done-preview'
                )->with($with)
                    ->render();

                return response()->json(['html' => $view], Response::HTTP_OK);
            }

            return view('frontend.freelancer.job-done-preview-page')->with($with);
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'html' => '',
                    'error' => true,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with('error', 'An error has occurred.');
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|void
     */
    public function getReviewJobDone(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $job = $this->jobService->getById($id);
                $reviews = $this->reviewService->getReviewJobByJobId($id);
                $myReview = $this->reviewService->getMyReviewJob(auth()->id(), $id, JOB_REVIEW);
                $view = view('frontend.freelancer.data-review-job-done', compact('job', 'reviews', 'myReview'))->render();
                return response()->json(['html' => $view], Response::HTTP_OK);
            } catch (Exception $e) {
                return response()->json([
                    'html' => '',
                    'error' => true,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * @param int $jobId
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function viewStatus($jobId, Request $request)
    {
        $job = $this->jobService->getJobApplicationById($jobId, auth()->id());
        $user = $this->userService->getUserInfo(auth()->id());
        $count = $this->jobService->getCountSearchJob($request, config('paging.quantity'), $user);
        $isFreelancer = true;

        $orderBy = $request->orderBy ?? TYPE_SORT_DESC;

        return view('frontend.freelancer.payment.view_status')->with([
            'job' => $job,
            'count' => $count,
            'orderBy' => $orderBy,
            'previousJob' => $this->jobService->getJobApplicationPreview(PREVIOUS, $jobId, auth()->id(), $request),
            'nextJob' => $this->jobService->getJobApplicationPreview(NEXT, $jobId, auth()->id(), $request),
            'myReview' => $this->reviewService->getMyReviewJob(auth()->id(), $jobId, JOB_REVIEW),
            'jobReviews' => $this->reviewService->getReviewJobByJobId($jobId),
            'isFreelancer' => $isFreelancer,
            'user' => $user,
            'jobApplied' => [$jobId]
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function detailJobHighlight(Request $request)
    {
        $jobId = $request->query('highlight_job');

        $orderBy = TYPE_SORT_DESC;
        $totalJob = $this->jobService->count();
        $job = $this->jobService->getById($jobId);
        $user = $this->userService->getUserInfo(Auth::user()->id);
        $previousJob = $this->jobService->getJobPreview(PREVIOUS, $job, $user, $request);
        $nextJob = $this->jobService->getJobPreview(NEXT, $job, $user, $request);
        $myReview = $this->reviewService->getMyReviewJob(auth()->id(), $job->id, JOB_REVIEW);
        $jobReviews = $this->reviewService->getReviewJobByJobId($job->id);
        $jobApplied = $this->jobService->getAllMyJobApplied(auth()->id());
        $isFreelancer = true;
        return view(
            'frontend.freelancer.job-highlight',
            compact('totalJob', 'job', 'previousJob', 'nextJob', 'jobReviews', 'myReview', 'jobApplied', 'user', 'isFreelancer', 'orderBy')
        );
    }
}
