<?php

namespace App\Domains\Portfolio\Services;

use App\Domains\Auth\Models\User;
use App\Domains\Portfolio\Models\Portfolio;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Class PortfolioService
 * @package App\Domains\Timezone\Services
 */
class PortfolioService extends BaseService
{
    public const PATH_TO_PORTFOLIO = '/public/portfolios/';
    public const DISK = 'azure';

    /**
     * PortfolioService constructor.
     * @param Portfolio $portfolio
     */
    public function __construct(Portfolio $portfolio)
    {
        $this->model = $portfolio;
    }

    /**
     * @param string $id
     * @param array $data
     * @param bool $isUser
     * @return bool
     */
    public function setImagePortfolio(string $id, array $data = [], bool $isUser = true): bool
    {
        if (count($data['file_upload'])) {
            foreach ($data['file_upload'] as $key => $file) {
                $fileName = $key . (now()->timestamp) . '.' . $file->extension();
                $file->storeAs($this::PATH_TO_PORTFOLIO, $fileName, $this::DISK);
                $fileNameUpload = $data['file_name'][$key];
                if ($isUser) {
                    $this->model::create(['user_id' => $id, 'name' => $fileNameUpload, 'file' => $fileName]);
                } else {
                    $this->model::create(['job_id' => $id, 'name' => $fileNameUpload, 'file' => $fileName]);
                }
            }
            return true;
        }
        return false;
    }

    public function getByIdAndCondition($id, $condition_id, $type)
    {
        if ($type == TYPE_JOB) {
            return $this->model->where('id', $id)->where('job_id', $condition_id)->first();
        }

        return $this->model->where('id', $id)->where('user_id', $condition_id)->first();
    }

    public function updatePortfolio($params, $id, $type)
    {
        $params['file_upload'] = $params['file_upload'] ?? [];
        if (isset($params['portfolios_delete'])) {
            foreach ($params['portfolios_delete'] as $portfolios_id) {
                $portfolios = $this->getByIdAndCondition($portfolios_id, $id, $type);
                $fileName = $portfolios->file;
                if ($portfolios) {
                    $this->deleteById($portfolios_id);
                }
                Storage::delete($this::PATH_TO_PORTFOLIO . $fileName);
            }
        }
        $this->setImagePortfolio($id, $params, $type == TYPE_USER);
    }

    /**
     * @param int $jobId
     * @return array|\Illuminate\Support\Collection
     */
    public function getPhotoByJobId(int $jobId)
    {
        return $this->model->select('file')->where('job_id', $jobId)->pluck('file');
    }

    /**
     * @param array|\Illuminate\Support\Collection $files
     * @return bool
     */
    public function deleteFileInStorage($files)
    {
        foreach ($files as $file) {
            if (Storage::disk($this::DISK)->exists($this::PATH_TO_PORTFOLIO . $file)) {
                Storage::disk($this::DISK)->delete($this::PATH_TO_PORTFOLIO . $file);
            }
        }

        return true;
    }

    /**
     * @param $id
     * @param $params
     * @param $type
     */
    public function updateFileName($id, $params, $type)
    {
        if ($type == TYPE_USER) {
            $files = $this->model->where('user_id', $id)->get()->pluck('id');
        } else {
            $files = $this->model->where('job_id', $id)->get()->pluck('id');
        }

        foreach ($params as $key => $param) {
            $this->model->where('id', $files[$key])->update(['name' => $param]);
        }
    }

    /**
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function findAndDownload(string $filename)
    {
        $file = $this->model
            ->where('file', $filename)
            ->with('job:id,user_id')
            ->first();

        abort_if(
            !$file ||
            !Storage::disk($this::DISK)->exists($this::PATH_TO_PORTFOLIO . $filename) ||
            (!auth()->user()->isAdmin() && !is_null($file->job) && optional($file->job)->user_id != auth()->id()),
            Response::HTTP_NOT_FOUND
        );
        return Storage::disk($this::DISK)->download($this::PATH_TO_PORTFOLIO . $filename, $file->name);
    }
}
