<?php

namespace App\Domains\Sector\Services;

use App\Domains\Sector\Models\Sector;
use App\Services\BaseService;

/**
 * Class SectorService.
 */
class SectorService extends BaseService
{
    /**
     * @param Sector $sector
     */
    public function __construct(Sector $sector)
    {
        $this->model = $sector;
    }

}
