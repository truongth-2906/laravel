<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Employer;

use App\Domains\Auth\Http\Requests\Backend\Employer\UpdateEmployerRequest;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\UserService;
use App\Domains\Auth\Services\EmployerService;
use App\Domains\Job\Models\Job;
use App\Domains\Job\Services\JobService;
use App\Domains\JobApplication\Services\JobApplicationService;
use App\Domains\Review\Http\Requests\Frontend\StoreReviewRequest;
use App\Domains\JobApplication\Http\Requests\Frontend\UpdateStatusRequest;
use App\Domains\Review\Services\ReviewService;
use App\Exceptions\GeneralException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Domains\Company\Services\CompanyService;
use App\Domains\Portfolio\Services\PortfolioService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EmployerController
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @var EmployerService
     */
    protected EmployerService $employerService;

    /**
     * @var CompanyService
     */
    protected CompanyService $companyService;

    /**
     * @var PortfolioService
     */
    protected PortfolioService $portfolioService;

    /**
     * @var JobService
     */
    protected JobService $jobService;

    /**
     * @var ReviewService
     */
    protected ReviewService $reviewService;

    /**
     * @var JobApplicationService
     */
    protected JobApplicationService $jobApplicationService;

    /**
     * @param UserService $userService
     * @param EmployerService $employerService
     * @param CompanyService $companyService
     * @param PortfolioService $portfolioService
     * @param JobService $jobService
     * @param ReviewService $reviewService
     * @param JobApplicationService $jobApplicationService
     */
    public function __construct(
        UserService $userService,
        EmployerService $employerService,
        CompanyService $companyService,
        PortfolioService $portfolioService,
        JobService $jobService,
        ReviewService $reviewService,
        JobApplicationService $jobApplicationService
    ) {
        $this->userService = $userService;
        $this->employerService = $employerService;
        $this->companyService = $companyService;
        $this->portfolioService = $portfolioService;
        $this->jobService = $jobService;
        $this->reviewService = $reviewService;
        $this->jobApplicationService = $jobApplicationService;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $userLogin = auth()->user();
        $userId = $userLogin->id;
        $totalJobs = $this->jobService->where('user_id', $userId)->count();
        $jobs = $this->jobService->search($request, config('paging.quantity'), $userId);
        $orderBy = $request->orderBy ?? TYPE_SORT_DESC;
        if ($request->ajax()) {
            $view = view('frontend.employer.table', compact('jobs', 'orderBy'))->render();

            return response()->json(['html' => $view]);
        }

        return view('frontend.user.dashboard', compact('totalJobs', 'jobs', 'orderBy'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function findFreelancer(Request $request)
    {
        $freelancers = $this->userService->search($request, User::TYPE_FREELANCER, config('paging.quantity'));
        $orderByType = $request->orderByType ?? 'DESC';
        $orderByField = $request->orderByField ?? '';
        $orderByAvailable = $request->orderByAvailable ?? 'DESC';
        if ($request->ajax()) {
            $freelancers = $this->userService->search($request, User::TYPE_FREELANCER, config('paging.quantity'));
            $view = view(
                'frontend.employer.find.table',
                compact('freelancers', 'orderByAvailable', 'orderByType', 'orderByField')
            )->render();
            return response()->json(
                [
                    'html' => $view,
                    'total' => $freelancers->total()
                ]
            );
        }
        return view(
            'frontend.employer.find.index',
            compact('freelancers', 'orderByAvailable', 'orderByType', 'orderByField')
        );
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function profile($id)
    {
        $employer = $this->employerService->getById($id);
        if ($employer && $employer->isEmployer()) {
            return view('frontend.employer.profile', compact('employer'));
        }
        return redirect()->route(homeRoute())->with('error', __('The employer you requested could not be found.'));
    }

    /**
     * @return Application|Factory|View
     */
    public function setting()
    {
        $employer = auth()->user();
        $lengthBio = User::BIO_LENGTH;

        return view('frontend.employer.setting', compact('employer', 'lengthBio'));
    }

    /**
     * @param UpdateEmployerRequest $request
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function updateDetails(UpdateEmployerRequest $request): RedirectResponse
    {
        $id = auth()->user()->id;

        if ($this->employerService->updateByEmployer(
            $request->all(),
            $id,
            $this->companyService,
            $this->portfolioService
        )) {
            return redirect()->route('frontend.employer.setting')->with(
                'message',
                __('The employer was successfully updated.')
            );
        }
        return redirect()->back()->withInput()->with('error', __('The employer update failed.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function previewFreelancer(Request $request)
    {
        $freelancer = $this->userService->getById($request->id);
        $previousFreelancer = $this->userService->getFreelancerPreview(PREVIOUS, $freelancer, $request);
        $nextFreelancer = $this->userService->getFreelancerPreview(NEXT, $freelancer, $request);
        $myReview = $this->reviewService->getMyReviewJob(auth()->id(), $freelancer->id, USER_REVIEW);
        $freelancerReviews = $this->reviewService->getReviewByUserId($freelancer->id);
        $view = view(
            'frontend.employer.find.freelancer_preview',
            compact('freelancer', 'previousFreelancer', 'nextFreelancer', 'freelancerReviews', 'myReview')
        )
            ->render();

        return response()->json(['html' => $view]);
    }

    /**
     * @param StoreReviewRequest $request
     * @return JsonResponse
     */
    public function addReviewFreelancer(StoreReviewRequest $request)
    {
        $this->reviewService->createReviewFreelancer($request);
        $reviews = $this->reviewService->getReviewByUserId($request->user_id);
        $view = view('frontend.freelancer.review', compact('reviews'))->render();

        return response()->json(['html' => $view]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function currentPageFreelancer(Request $request)
    {
        $freelancer = $this->userService->getById($request->id);
        $page = $this->userService->getPage($freelancer, $request);

        return response()->json(['page' => $page]);
    }

    /**
     * @return Application|Factory|View|JsonResponse
     */
    public function listPayment()
    {
        return view('frontend.employer.payment.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function previewJob(Request $request)
    {
        $job = $this->jobService->getById($request->id);
        if (optional($job)->status == Job::STATUS_CLOSE) {
            return response()->json(['html' => false]);
        }
        $user = $this->userService->getUserInfo(Auth::user()->id);
        $previousJob = $this->jobService->getJobPreviewOfEmployer(PREVIOUS, $job, $request);
        $nextJob = $this->jobService->getJobPreviewOfEmployer(NEXT, $job, $request);
        $jobReviews = $this->reviewService->getReviewJobByJobId($job->id);
        $myReview = $this->reviewService->getMyReviewJob(auth()->id(), $job->id, JOB_REVIEW);
        $jobApplied = $this->jobService->getAllMyJobApplied(auth()->id());
        $markDone = $this->jobApplicationService->checkMarkDoneJob($job);

        $view = view(
            'frontend.freelancer.job-preview',
            compact('job', 'previousJob', 'nextJob', 'jobReviews', 'myReview', 'jobApplied', 'user', 'markDone')
        )
            ->render();

        return response()->json(['html' => $view]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function currentPageJob(Request $request)
    {
        $page = $this->jobService->getCurrentPageJob($request);
        return response()->json(['page' => $page]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listFreelancerOfJob(Request $request)
    {
        $freelancers = $this->jobService->listFreelancerJob($request);
        $view = view('frontend.employer.table_freelancer_job', compact('freelancers'))->render();

        return response()->json(['html' => $view]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function detailFreelancerApply(Request $request)
    {
        $freelancerDetail = $this->userService->detailFreelancer($request);
        $view = view('frontend.employer.detail_freelancer_apply', compact('freelancerDetail'))->render();

        return response()->json(['html' => $view]);
    }

    /**
     * @param UpdateStatusRequest $request
     * @return JsonResponse
     */
    public function updateStatusJobApplication(UpdateStatusRequest $request)
    {
        if (is_null(auth()->user()->escrow_email)) {
            return response()->json([
                'message' => 'Please add escrow account before approved freelancer!',
                'status' => Response::HTTP_MOVED_PERMANENTLY,
                'url' => route('frontend.employer.payments.escrow_account.create', ['job_id' => $request->jobId])
            ]);
        }
        $job = $this->jobService->getJobWithUser($request->jobId);
        $update = $this->jobApplicationService->updateStatus($request, $job);
        if ($update) {
            return response()->json([
                'update' => 'Update status freelancer application success.',
                'redirect_url' => $update,
                'status' => Response::HTTP_OK
            ]);
        }

        return response()->json(
            [
                'update' => 'Update status freelancer application failed.',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     * @throws \Throwable
     */
    public function applications($id, Request $request)
    {
        $job = $this->jobService->findMyJobOpen($id);
        if (!$job) {
            return redirect()->back()->with('warning', __('This job is closed.'));
        }
        $user = $this->userService->getUserInfo(Auth::user()->id);
        $previousJob = $this->jobService->getJobPreviewOfEmployer(PREVIOUS, $job, $request);
        $nextJob = $this->jobService->getJobPreviewOfEmployer(NEXT, $job, $request);
        $jobReviews = $this->reviewService->getReviewJobByJobId($job->id);
        $myReview = $this->reviewService->getMyReviewJob(auth()->id(), $job->id, JOB_REVIEW);
        $jobApplied = $this->jobService->getAllMyJobApplied(auth()->id());
        $markDone = $this->jobApplicationService->checkMarkDoneJob($job);
        $totalJobs = $this->jobService->getCountMyJob();

        return view(
            'frontend.job.applicant',
            compact(
                'job',
                'previousJob',
                'nextJob',
                'jobReviews',
                'myReview',
                'jobApplied',
                'user',
                'markDone',
                'totalJobs'
            )
        );
    }
}
