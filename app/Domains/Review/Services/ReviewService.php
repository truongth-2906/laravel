<?php

namespace App\Domains\Review\Services;

use App\Domains\Review\Models\Review;
use App\Services\BaseService;

/**
 * Class ReviewService
 */
class ReviewService extends BaseService
{
    /**
     * @param Review $review
     */
    public function __construct(Review $review)
    {
        $this->model = $review;
    }

    /**
     * @param $jobId
     * @return mixed
     */
    public function getReviewJobByJobId($jobId)
    {
        return $this->model->where('job_id', $jobId)->orderBy('created_at', TYPE_SORT_DESC)->get();
    }

    /**
     * @param int $userId
     * @param int $id
     * @param string $column
     * @return mixed
     */
    public function getMyReviewJob($userId, $id, $column)
    {
        return $this->model->where([
            [$column, $id], ['review_id', $userId]
        ])->first();
    }

    /**
     * @param $request
     * @return false
     */
    public function createReviewJob($request)
    {
        $review = $this->getMyReviewJob(auth()->id(), $request->job_id, JOB_REVIEW);
        if ($review) {
            return false;
        }

        return $this->model::create([
            'star' => $request->star,
            'description' => $request->description,
            'job_id' => $request->job_id,
            'review_id' => auth()->id()
        ]);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getReviewByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->orderBy('created_at', TYPE_SORT_DESC)->get();
    }

    /**
     * @param $request
     * @return false
     */
    public function createReviewFreelancer($request)
    {
        $review = $this->getMyReviewJob(auth()->id(), $request->user_id, USER_REVIEW);
        if ($review) {
            return false;
        }

        return $this->model::create([
            'star' => $request->star,
            'description' => $request->description,
            'user_id' => $request->user_id,
            'review_id' => auth()->id()
        ]);
    }
}
