<?php

namespace App\Domains\Company\Http\Controllers\Frontend;

use App\Domains\Company\Http\Requests\Backend\StoreCompanyRequest;
use App\Domains\Company\Services\CompanyService;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    /**
     * @var CompanyService
     */
    protected CompanyService $companyService;

    /**
     * @param CompanyService $companyService
     */
    public function __construct
    (
        CompanyService $companyService
    ) {
        $this->companyService = $companyService;
    }

    /**
     * @param StoreCompanyRequest $request
     * @return mixed
     * @throws GeneralException
     */
    public function store(StoreCompanyRequest $request)
    {
        return $this->companyService->store($request->all());
    }
}
