<?php

namespace App\Domains\Timezone\Services;

use App\Domains\Timezone\Models\Timezone;
use App\Services\BaseService;

/**
 * Class TimezoneService
 */
class TimezoneService extends BaseService
{
    /**
     * @param Timezone $timezone
     */
    public function __construct(Timezone $timezone)
    {
        $this->model = $timezone;
    }
}
