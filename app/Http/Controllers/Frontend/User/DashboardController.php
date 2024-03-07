<?php

namespace App\Http\Controllers\Frontend\User;

use App\Domains\Auth\Services\UserService;
use App\Domains\Job\Services\JobService;
use Illuminate\Http\Request;

/**
 * Class DashboardController.
 */
class DashboardController
{
    /**
     * @var
     */
    protected $jobService;

    /**
     * @var
     */
    protected $userService;

    /**
     * @param JobService $jobService
     * @param UserService $userService
     */
    public function __construct(JobService $jobService, UserService $userService)
    {
        $this->jobService = $jobService;
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $userLogin = auth()->user();
        if ($userLogin->isEmployer()) {
            $userId = $userLogin->id;
            $totalJobs = $this->jobService->where('user_id', $userId)->count();
            $jobs = $this->jobService->search($request, config('paging.quantity'), $userId);
        } else {
            $totalJobs = $this->jobService->count();
            $userInfo = $this->userService->getUserInfo($userLogin->id);
            $jobs = $this->jobService->searchFreelancerJob($request, config('paging.quantity'), $userInfo);
        }

        $orderBy = $request->orderBy ?? 'DESC';
        if ($request->ajax()) {
            $table = $userLogin->isEmployer() ? 'frontend.employer.table' : 'frontend.freelancer.table';
            $view = view($table, compact('jobs', 'orderBy'))->render();

            return response()->json(['html' => $view]);
        }

        return view('frontend.user.dashboard', compact('totalJobs', 'jobs', 'orderBy'));
    }
}
