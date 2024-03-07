<?php

namespace App\Domains\Job\Exports\Backend;

use App\Domains\Job\Services\JobService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class JobExport implements FromView
{
    protected $request;

    /**
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $jobService = resolve(JobService::class);
        return view('backend.job.export', [
            'jobs' => $jobService->getDataExport($this->request)
        ]);
    }
}
