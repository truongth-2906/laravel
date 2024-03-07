<?php

namespace App\Domains\Job\Models\Traits\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class JobScope.
 */
trait JobScope
{
    /**
     * @param $query
     * @param $request
     * @return mixed
     */
    public function scopeSearch($query, $request)
    {
        $orderByType = in_array(strtoupper($request->orderByType ?? ''), ['ASC', 'DESC']) ? strtoupper($request->orderByType) : 'DESC';
        return $query
        ->when($request->checkBoxIds, function ($query) use ($request) {
            $query->whereIn('id', $request->checkBoxIds);
        })
        ->when($request->userId, function ($query) use ($request) {
            $query->where('user_id', $request->userId);
        })
        ->when($this->isValidatedSortQuery($request), function ($e) use ($request, $orderByType) {
            if ($this->isSortByCustomerName($request)) {
                $e->orderByRaw('(SELECT users.name FROM users WHERE users.id = jobs.user_id) ' . $orderByType);
            } else {
                $e->orderBy($request->orderByField, $orderByType);
            }
        })
        ->when(!$request->has('orderByField') && $request->orderBy, function ($e) use ($request) {
            $e->orderBy('status', $request->orderBy);
        })
        ->orderBy('id', 'DESC');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeHasOpen($query)
    {
        return $query->where(JOBS_TABLE_NAME . '.status', $this::STATUS_OPEN);
    }

    /**
     * @param Builder $query
     * @param $userId
     * @return void
     */
    public function scopeJoinToJobApplication(Builder $query, $userId)
    {
        $query
            ->join(JOB_APPLICATIONS_TABLE_NAME, function ($join) use ($userId) {
                $join->on(JOB_APPLICATIONS_TABLE_NAME . '.job_id', '=', JOBS_TABLE_NAME . '.id')
                    ->where(JOB_APPLICATIONS_TABLE_NAME . '.user_id', $userId);
            });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @param string $order
     * @return void
     */
    public function scopeJobApplicationDefaultQuery(Builder $query, int $userId, string $order = 'ASC')
    {
        $query->select([
            JOBS_TABLE_NAME . '.id',
            JOBS_TABLE_NAME . '.name',
            JOBS_TABLE_NAME . '.user_id',
            JOBS_TABLE_NAME . '.country_id',
            JOBS_TABLE_NAME . '.company_id',
            JOBS_TABLE_NAME . '.experience_id',
            JOBS_TABLE_NAME . '.timezone_id',
            JOB_APPLICATIONS_TABLE_NAME . '.status as job_application_status',
            JOB_APPLICATIONS_TABLE_NAME . '.id as job_application_id'
        ])
            ->hasOpen()
            ->with('company:id,name,logo')
            ->with('user:id,name')
            ->with('categories:id,name,class')
            ->with('country:id,name,code')
            ->with('experience:id,name')
            ->with('timezone:id,offset')
            ->joinToJobApplication($userId)
            ->orderBy('job_application_status', $order)
            ->orderBy('job_application_id', 'DESC');
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isValidatedSortQuery(Request $request)
    {
        return in_array($request->orderByField, $this::FIELDS_ALLOWED_SORT) && in_array($request->orderByType, [TYPE_SORT_ASC, TYPE_SORT_DESC]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isSortByCustomerName(Request $request)
    {
        return $request->orderByField == $this->getFieldAllowSort('employer_name');
    }
}
