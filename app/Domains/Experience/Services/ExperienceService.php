<?php

namespace App\Domains\Experience\Services;

use App\Domains\Experience\Models\Experience;
use App\Services\BaseService;

/**
 * Class ExperienceService.
 */
class ExperienceService extends BaseService
{
    /**
     * @param Experience $experience
     */
    public function __construct(Experience $experience)
    {
        $this->model = $experience;
    }

}
