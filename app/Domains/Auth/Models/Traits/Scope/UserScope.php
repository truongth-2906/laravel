<?php

namespace App\Domains\Auth\Models\Traits\Scope;

use Illuminate\Http\Request;

/**
 * Class UserScope.
 */
trait UserScope
{
    /**
     * @param $query
     * @param $request
     * @param $type
     * @param array $usersOnline
     * @return mixed
     */
    public function scopeSearch($query, $request, $type, array $usersOnline = [])
    {
        $orderByType = in_array(strtoupper($request->orderByType ?? ''), ['ASC', 'DESC']) ? strtoupper($request->orderByType) : 'DESC';
        return $query
            ->select('*')
            ->selectOnlineStatus($usersOnline)
            ->where(function ($query) use ($request, $type) {
                $query->where('type', $type)
                    ->when($request->checkBoxIds, function ($query) use ($request) {
                        $query->whereIn('id', $request->checkBoxIds);
                    })->when($request->countryId, function ($query) use ($request) {
                        $query->where('country_id', $request->countryId);
                    })->when($request->companyId, function ($query) use ($request) {
                        $query->where('company_id', $request->companyId);
                    })->when($request->experienceId, function ($query) use ($request) {
                        $query->where('experience_id', $request->experienceId);
                    })->where(function ($query) use ($request) {
                        $query->where('name', 'like', '%' . escapeLike($request->keyword) . '%');
                    })->when($request->categoryIds, function ($query) use ($request) {
                        $query->whereHas('categories', function ($query) use ($request) {
                            $query->whereIn('category_id', $request->categoryIds);
                        });
                    })->when($this->isValidatedSortActiveQuery($request), function ($query) use ($request) {
                        $request->is_active == $this::IS_ACTIVE ? $query->where(
                            'active',
                            $this::IS_ACTIVE
                        ) : $query->where('active', '!=', $this::IS_ACTIVE);
                    });
            })->when($this->isValidatedSortQuery($request), function ($query) use ($request, $orderByType) {
                if ($this->isSortBySectorName($request)) {
                    $query->orderByRaw(
                        '(SELECT sectors.name FROM sectors WHERE sectors.id = users.sector_id) ' . $orderByType
                    );
                } else {
                    $query->orderBy(
                        $request->orderByField != $this::FIELDS_ALLOWED_SORT['is_online'] ? 'users.' . $request->orderByField : $request->orderByField,
                        $orderByType
                    );
                }
            })
            ->userNotHidden()
            ->orderBy('users.score', 'DESC')
            ->orderBy('users.id', 'DESC');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOnlyDeactivated($query)
    {
        return $query->whereActive(false);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOnlyActive($query)
    {
        return $query->whereActive(true);
    }

    /**
     * @param $query
     * @param $type
     * @return mixed
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeAllAccess($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', config('base.access.role.admin'));
        });
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeAdmins($query)
    {
        return $query->where('type', $this::TYPE_ADMIN);
    }

    /**
     * @param $query
     * @return bool
     */
    public function scopeUsers($query): bool
    {
        return $this->scopeEmployers($query) || $this->scopeFreelancers($query);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFreelancers($query)
    {
        return $query->where('type', $this::TYPE_FREELANCER);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeEmployers($query)
    {
        return $query->where('type', $this::TYPE_EMPLOYER);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeHasAvailable($query)
    {
        return $query->where('available', $this::AVAILABLE);
    }

    /**
     * @param $query
     * @param $request
     * @return mixed
     */
    public function scopeUser($query, $request)
    {
        return $query->where(function ($query) {
            $query->where('type', $this::TYPE_EMPLOYER)
                ->orWhere('type', $this::TYPE_FREELANCER);
        })
            ->whereNotNull('email_verified_at')
            ->when($request->keyword, function ($q) use ($request) {
                $q->where('name', 'like', '%' . escapeLike($request->keyword) . '%');
            });
    }

    /**
     * @param $query
     * @param array $usersOnline
     * @return mixed
     */
    public function scopeSelectOnlineStatus($query, array $usersOnline = [])
    {
        $usersOnline = implode(', ', $usersOnline);

        return $query->when($usersOnline, function ($e) use ($usersOnline) {
            $e->selectRaw("users.id IN ($usersOnline) as is_online");
        })
            ->when(!$usersOnline, function ($e) {
                $e->selectRaw("FALSE as is_online");
            });
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isValidatedSortQuery(Request $request)
    {
        return in_array($request->orderByField, $this::FIELDS_ALLOWED_SORT) && in_array(
                $request->orderByType,
                [TYPE_SORT_ASC, TYPE_SORT_DESC]
            );
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isValidatedSortActiveQuery(Request $request)
    {
        return !is_null($request->is_active) && in_array(
                $request->is_active,
                [$this::IS_ACTIVE, $this::IS_DECLINED, $this::IS_PENDING]
            );
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isSortBySectorName(Request $request)
    {
        return $request->orderByField == $this->getFieldAllowSort('sector_name');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeUserNotHidden($query)
    {
        /** @var \App\Domains\Auth\Models\User */
        $user = auth()->user();

        $query->when($user && $user->isEmployer(), function ($e) {
            $e->where('is_hidden', false);
        })->when($user && $user->isFreelancer(), function ($e) use ($user) {
            $e->whereRaw('IF((users.id <> ? AND users.is_hidden = ?) OR users.id = ?, 1, 0) = 1', [$user->id, 0, $user->id]);
        });
    }
}
