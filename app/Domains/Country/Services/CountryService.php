<?php

namespace App\Domains\Country\Services;

use App\Domains\Country\Models\Country;
use App\Services\BaseService;

/**
 * Class CountryService.
 */
class CountryService extends BaseService
{
    /**
     * @param Country $country
     */
    public function __construct(Country $country)
    {
        $this->model = $country;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        return $this->model->select('id', 'name', 'code', 'calling_code')->get();
    }
}
