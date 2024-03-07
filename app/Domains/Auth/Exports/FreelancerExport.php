<?php

namespace App\Domains\Auth\Exports;

use App\Domains\Auth\Services\FreelancerService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class FreelancerExport implements FromView
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
        $freelancerService = resolve(FreelancerService::class);
        return view('backend.freelancer.export', [
            'freelancers' => $freelancerService->getDataExport($this->request, TYPE_FREELANCER)
        ]);
    }
}
