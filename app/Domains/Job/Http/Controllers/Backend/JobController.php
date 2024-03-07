<?php

namespace App\Domains\Job\Http\Controllers\Backend;

use App\Domains\Auth\Services\UserService;
use App\Domains\Job\Exports\Backend\JobExport;
use App\Domains\Job\Models\Job;
use App\Domains\Job\Services\JobService;
use App\Exceptions\GeneralException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Domains\Job\Http\Requests\Backend\JobRequest;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
     * @param JobService $jobService
     * @param UserService $userService
     */
    public function __construct(
        JobService $jobService,
        UserService $userService
    ) {
        $this->jobService = $jobService;
        $this->userService = $userService;
    }

    /**
     * list job
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $jobs = $this->jobService->search($request, config('paging.quantity'));
        $orderByType = $request->orderByType ?? 'DESC';
        $orderByField = $request->orderByField ?? '';

        if ($request->ajax()) {
            $jobs = $this->jobService->search($request, config('paging.quantity'));
            $view = view('backend.job.table', compact('jobs', 'orderByType', 'orderByField'))->render();
            return response()->json(['html' => $view]);
        }
        return view('backend.job.index', compact('jobs', 'orderByType', 'orderByField'));
    }

    /**
     * @return Application|Factory|View|JsonResponse
     */
    public function create(Request $request)
    {
        if ($request->all()) {
            return response()->json($this->userService->getUserByCompanyId($request->company_id));
        }
        $maxDescription = Job::MAX_DESCRIPTION;
        return view('backend.job.create', compact('maxDescription'));
    }

    /**
     * @param JobRequest $request
     * @return RedirectResponse
     * @throws GeneralException|Throwable
     */
    public function store(JobRequest $request): RedirectResponse
    {
        $this->jobService->store($request->all());

        return redirect()->route('admin.job.index')->with('message', __('Job added Successfully'));
    }

    /**
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $job = $this->jobService->getForEdit($id);
        if ($job) {
            $userCompanies = $this->userService->getUserByCompanyId($job->company_id);
            $maxDescription = Job::MAX_DESCRIPTION;

            return view('backend.job.edit', compact('job', 'userCompanies', 'maxDescription'));
        }

        return redirect()->back()->with('error', 'Job not found.');
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception|Exception
     */
    public function delete($id)
    {
        try {
            $this->jobService->delete($id);

            return redirect()->back()->with('message', __('Delete job success.'));
        } catch (Exception $e) {
            return redirect()->back()->with('message', __('Delete job failed.'));
        }
    }

    /**
     * @param JobRequest $request
     * @param $id
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function update(JobRequest $request, $id): RedirectResponse
    {
        $this->jobService->update($request->all(), $id);

        return redirect()->route('admin.job.index')->with('message', __('The job was successfully update.'));
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(Request $request)
    {
        return Excel::download(new JobExport($request), 'jobs_' . now()->format('Y-m-d') . '.csv');
    }
}
