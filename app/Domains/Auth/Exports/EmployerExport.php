<?php

namespace App\Domains\Auth\Exports;

use App\Domains\Auth\Services\EmployerService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EmployerExport implements FromView
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
        $employerService = resolve(EmployerService::class);
        return view('backend.employer.export', [
            'employers' => $employerService->getDataExport($this->request, TYPE_EMPLOYER)
        ]);
    }
}
