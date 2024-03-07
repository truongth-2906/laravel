<?php

namespace App\Domains\Company\Services;

use App\Domains\Company\Models\Company;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class CompanyService.
 */
class CompanyService extends BaseService
{
    /**
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->model = $company;
    }

    /**
     * @param $request
     * @return mixed
     * @throws GeneralException
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $company = $this->model::create($request);
            DB::commit();
            return $company;
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem creating this company. Please try again.'));
        }

    }

    /**
     * @param $data
     * @return bool
     * @throws GeneralException
     */
    public function update($data)
    {
        DB::beginTransaction();
        try {
            $fileName = (now()->timestamp) . '.' . $data['logo']->extension();
            $data['logo']->storeAs('/public/companies', $fileName, 'azure');

            $company = $this->getById($data['company_id'])->update([
                'logo' => $fileName
            ]);

            DB::commit();
            return $company;
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem update this company. Please try again.'));
        }
    }
}
