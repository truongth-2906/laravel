<?php

namespace App\Domains\Saved\Services;

use App\Domains\Saved\Models\Saved;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SavedService
 * @package App\Domains\Saved\Services
 */
class SavedService extends BaseService
{
    /**
     * @param Saved $saved
     */
    public function __construct(Saved $saved)
    {
        $this->model = $saved;
    }

    /**
     * @param $data
     * @param $jobService
     * @return bool
     * @throws GeneralException
     * @throws Throwable
     */
    public function saveJob($data, $jobService)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $job = $jobService->getById($data['job_id']);
            $saveJobIds = $user->jobsSaved->pluck('saved_id');
            if ($saveJobIds->contains($data['job_id'])) {
                $job->savers()->delete($this->model->user()->associate($user));
            } else {
                $job->savers()->save($this->model->user()->associate($user));
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * @param $data
     * @param $freelancerService
     * @return bool
     * @throws GeneralException
     * @throws Throwable
     */
    public function saveFreelancer($data, $freelancerService)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $freelancer = $freelancerService->getById($data['freelancer_id']);
            $saveFreelancerIds = $user->freelancersSaved->pluck('saved_id');
            if ($saveFreelancerIds->contains($data['freelancer_id'])) {
                $freelancer->savers()->delete($this->model->user()->associate($user));
            } else {
                $freelancer->savers()->save($this->model->user()->associate($user));
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
