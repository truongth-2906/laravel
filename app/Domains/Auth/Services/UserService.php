<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Events\User\UserCreated;
use App\Domains\Auth\Events\User\UserDeleted;
use App\Domains\Auth\Events\User\UserDestroyed;
use App\Domains\Auth\Events\User\UserRestored;
use App\Domains\Auth\Events\User\UserStatusChanged;
use App\Domains\Auth\Events\User\UserUpdated;
use App\Domains\Auth\Models\User;
use App\Domains\Job\Services\JobService;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use InvalidArgumentException;
use Throwable;

/**
 * Class UserService.
 */
class UserService extends BaseService
{
    protected const ONLINE_MEMBERS_TABLE = 'presence-online:members';

    /** @var JobService */
    protected $jobService;

    /**
     * UserService constructor.
     *
     * @param User $user
     * @param JobService $jobService
     */
    public function __construct(User $user, JobService $jobService)
    {
        $this->model = $user;
        $this->jobService = $jobService;
    }

    /**
     * @param $request
     * @param $type
     * @param bool $perPage
     * @return mixed
     */
    public function search($request, $type, $perPage = false)
    {
        $usersOnline = $this->getUsersOnlineId($type);

        if (is_numeric($perPage)) {
            return $this->model::search($request, $type, $usersOnline)->paginate($perPage);
        }

        return $this->model::search($request, $type, $usersOnline)->get();
    }

    /**
     * @param $type
     * @param bool|int $perPage
     * @return mixed
     */
    public function getByType($type, $perPage = false)
    {
        if (is_numeric($perPage)) {
            return $this->model::byType($type)->paginate($perPage);
        }

        return $this->model::byType($type)->get();
    }

    /**
     * @param array $data
     * @return mixed
     *
     * @throws GeneralException
     */
    public function registerUser(array $data = []): User
    {
        DB::beginTransaction();

        try {
            $user = $this->createUser($this->formatData($data));

            $user->updateFreelancerScore();
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating your account.'));
        }

        DB::commit();

        return $user;
    }

    /**
     * @param $info
     * @param $provider
     * @return mixed
     *
     * @throws GeneralException
     */
    public function registerProvider($info, $provider): User
    {
        $user = $this->model::where('email', $info->email)->first();

        throw_if(!$user, Exception::class, __('passwords.user'), Response::HTTP_NOT_FOUND);

        if (!$user->provider && $provider) {
            $user->provider = $provider;
        }

        if (!$user->provider_id && $info->id) {
            $user->provider_id = $info->id;
        }

        if ($user->isDirty()) {
            $user->save();
        }

        return $user;
    }

    /**
     * @param array $data
     * @return User
     *
     * @throws GeneralException
     * @throws Throwable
     */
    public function store(array $data = []): User
    {
        DB::beginTransaction();

        try {
            $user = $this->createUser([
                'type' => $data['type'],
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'email_verified_at' => isset($data['email_verified']) && $data['email_verified'] === $this->model::IS_ACTIVE ? now() : null,
                'active' => isset($data['active']) && $data['active'] === $this->model::IS_ACTIVE,
            ]);

            $user->syncRoles($data['roles'] ?? []);

            if (!config('base.access.user.only_roles')) {
                $user->syncPermissions($data['permissions'] ?? []);
            }
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this user. Please try again.'));
        }

        event(new UserCreated($user));

        DB::commit();

        // They didn't want to auto verify the email, but do they want to send the confirmation email to do so?
        if (
            !isset($data['email_verified'])
            && isset($data['send_confirmation_email'])
            && $data['send_confirmation_email'] === '1'
        ) {
            $user->sendEmailVerificationNotification();
        }

        return $user;
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     *
     * @throws Throwable
     */
    public function update(User $user, array $data = []): User
    {
        DB::beginTransaction();

        try {
            $user->update([
                'type' => $user->isMasterAdmin() ? $this->model::TYPE_ADMIN : $data['type'] ?? $user->type,
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            if (!$user->isMasterAdmin()) {
                // Replace selected roles/permissions
                $user->syncRoles($data['roles'] ?? []);

                if (!config('base.access.user.only_roles')) {
                    $user->syncPermissions($data['permissions'] ?? []);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this user. Please try again.'));
        }

        event(new UserUpdated($user));

        DB::commit();

        return $user;
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data = []): User
    {
        $user->name = $data['name'] ?? null;

        if ($user->canChangeEmail() && $user->email !== $data['email']) {
            $user->email = $data['email'];
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
            session()->flash('resent', true);
        }

        return tap($user)->save();
    }

    /**
     * @param User $user
     * @param $data
     * @param bool $expired
     * @return User
     *
     * @throws Throwable
     */
    public function updatePassword(User $user, $data, $expired = false): User
    {
        if (isset($data['current_password'])) {
            throw_if(
                !Hash::check($data['current_password'], $user->password),
                new GeneralException(__('That is not your old password.'))
            );
        }

        // Reset the expiration clock
        if ($expired) {
            $user->password_changed_at = now();
        }

        $user->password = $data['password'];

        return tap($user)->update();
    }

    /**
     * @param User $user
     * @param $status
     * @return User
     *
     * @throws GeneralException
     */
    public function mark(User $user, $status): User
    {
        if ($status === 0 && auth()->id() === $user->id) {
            throw new GeneralException(__('You can not do that to yourself.'));
        }

        if ($status === 0 && $user->isMasterAdmin()) {
            throw new GeneralException(__('You can not deactivate the administrator account.'));
        }

        $user->active = $status;

        if ($user->save()) {
            event(new UserStatusChanged($user, $status));

            return $user;
        }

        throw new GeneralException(__('There was a problem updating this user. Please try again.'));
    }

    /**
     * @param User $user
     * @return User
     *
     * @throws GeneralException
     */
    public function delete(User $user): User
    {
        if ($user->id === auth()->id()) {
            throw new GeneralException(__('You can not delete yourself.'));
        }

        if ($this->deleteById($user->id)) {
            event(new UserDeleted($user));

            return $user;
        }

        throw new GeneralException('There was a problem deleting this user. Please try again.');
    }

    /**
     * @param User $user
     * @return User
     *
     * @throws GeneralException
     */
    public function restore(User $user): User
    {
        if ($user->restore()) {
            event(new UserRestored($user));

            return $user;
        }

        throw new GeneralException(__('There was a problem restoring this user. Please try again.'));
    }

    /**
     * @param User $user
     * @return bool
     *
     * @throws GeneralException
     */
    public function destroy(User $user): bool
    {
        if ($user->forceDelete()) {
            event(new UserDestroyed($user));

            return true;
        }

        throw new GeneralException(__('There was a problem permanently deleting this user. Please try again.'));
    }

    /**
     * @param $companyId
     * @return mixed
     */
    public function getUserByCompanyId($companyId)
    {
        return User::where('company_id', $companyId)->get();
    }

    /**
     * @param array $data
     * @return User
     */
    protected function createUser(array $data = []): User
    {
        return $this->model::create([
            'type' => $data['type'] ?? $this->model::TYPE_FREELANCER,
            'name' => $data['firstname'] . ' ' . $data['lastname'] ?? null,
            'firstname' => $data['firstname'] ?? null,
            'lastname' => $data['lastname'] ?? null,
            'email' => $data['email'] ?? null,
            'calling_code_id' => $data['calling_code_id'],
            'phone_number' => $data['phone_number'],
            'password' => $data['password'] ?? null,
            'provider' => $data['provider'] ?? null,
            'provider_id' => $data['provider_id'] ?? null,
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'active' => $data['active'] ?? false,
        ]);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function formatData(array $data = []): array
    {
        return [
            'type' => $data['type'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'name' => $data['firstname'] . ' ' . $data['lastname'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone_number' => $data['phone_number'],
            'calling_code_id' => $data['calling_code'],
        ];
    }

    /**
     * @param $param
     * @return mixed
     */
    public function changePassword($param)
    {
        return $this->model::find(auth()->user()->id)->update(['password' => Hash::make($param['password'])]);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserInfo($userId)
    {
        return $this->model->where('id', $userId)->with(['categories', 'experience', 'country'])->first();
    }

    /**
     * @param User $user
     * @param $expired
     * @return User
     */
    public function updateVerifyEmail(User $user, $expired = null)
    {
        $user->update(['email_verified_at' => $expired ?? now()]);
        $user->updateFreelancerScore();
        return $user;
    }

    /**
     * @param string $type
     * @param \App\Domains\Auth\Models\User $freelancer
     * @param Request $request
     * @return int|null
     */
    public function getFreelancerPreview(string $type, User $freelancer, Request $request)
    {
        $result = null;
        $query = $this->model->newQuery()
            ->userNotHidden()
            ->where('available', User::AVAILABLE)
            ->search($request, User::TYPE_FREELANCER);

        if ($type == NEXT) {
            $result = $query->where('id', '<', $freelancer->id)->max('id');
        } else {
            $result = $query->where('id', '>', $freelancer->id)->min('id');
        }

        return $result;
    }

    /**
     * @param $user
     * @param $request
     * @return \Illuminate\Http\JsonResponse|int
     */
    public function getPage($user, $request)
    {
        try {
            $orderBy = $request->orderBy;
            $freelancer = $this->model->select('id')->where('type', User::TYPE_FREELANCER)
                ->userNotHidden()
                ->when($orderBy, function ($query) use ($orderBy) {
                    $query->orderBy('available', $orderBy);
                })->orderBy('id', 'DESC')->get();
            $indexUser = array_search($user->id, $freelancer->pluck('id')->toArray());
            return (int)($indexUser / config('paging.quantity')) + 1;
        } catch (Exception $e) {
            return response()->json(['page' => FIRST_PAGE]);
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function passbase($data)
    {
        $user = $this->model->where('email', $data['email'])->first();
        if ($user) {
            $user->update([
                'active' => $data['active'],
                'name' => $data['key']
            ]);
        }
        return $user;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function detailFreelancer($params)
    {
        return $this->model->where('users.id', $params->id)
            ->select([
                'users.id as user_id',
                'users.avatar as avatar',
                'users.bio as bio',
                'users.hours as hours',
                'users.rate_per_hours as rate_per_hours',
                'users.name as user_name',
                'jobs.description as description',
                'job_applications.created_at as date_apply',
                'job_applications.job_id as job_id',
                'job_applications.status as status'
            ])
            ->userNotHidden()
            ->join('job_applications', function ($query) {
                $query->on('job_applications.user_id', '=', 'users.id');
            })
            ->join(
                'jobs',
                function ($query) use ($params) {
                    $query->on('jobs.id', '=', 'job_applications.job_id')
                        ->where('jobs.id', $params->jobId);
                }
            )->first();
    }

    /**
     * @param array $types
     * @return array
     */
    protected function handleTypesArgument(array $types)
    {
        if ($keyAll = array_search('*', $types) !== false || empty($types)) {
            unset($types[$keyAll]);
            if (!in_array(User::TYPE_ADMIN, $types)) {
                $types[] = User::TYPE_ADMIN;
            }
            if (!in_array(User::TYPE_EMPLOYER, $types)) {
                $types[] = User::TYPE_EMPLOYER;
            }
            if (!in_array(User::TYPE_FREELANCER, $types)) {
                $types[] = User::TYPE_FREELANCER;
            }
        }

        return $types;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getUserPresenceOnline()
    {
        try {
            $data = Redis::get($this::ONLINE_MEMBERS_TABLE);
            $data = json_decode($data, true);

            throw_if(!is_array($data), InvalidArgumentException::class);

            return collect($data);
        } catch (Exception $e) {
            return collect();
        }
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getListUserChat($param)
    {
        $query = $this->model::user($param)
            ->userNotHidden()
            ->where('id', '!=', auth()->id())
            ->orderBy('created_at', 'asc');
        if ($param->offset) {
            return $query->offset($param->offset)->limit(config('paging.quantity'))->get();
        }

        return $query->paginate(config('paging.quantity'));
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getUserLatest($param)
    {
        return $this->model::user($param)
            ->userNotHidden()
            ->select('id')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * @param string|array[string]|null $types
     * @return array
     */
    public function getUsersOnlineId($types = null)
    {
        try {
            $types = (array)$types;
            $data = $this->getUserPresenceOnline();

            return $data
                ->when(count($types), function ($collection) use ($types) {
                    return $collection->whereIn('user_info.type', $types);
                })
                ->unique()
                ->pluck('user_id')
                ->toArray();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isOnline(int $userId)
    {
        try {
            $data = $this->getUserPresenceOnline();

            return $data->where('user_id', $userId)->isNotEmpty();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $user
     * @param array $categories
     * @return \Illuminate\Database\Eloquent\Model
     * @throws Throwable
     */
    public function addUserFromOldSystem($user, array $categories)
    {
        throw_if(
            !$user = $this->model->create($user),
            Exception::class,
            'Create user failed',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

        if (count($categories)) {
            $user->categories()->sync($categories);
        }

        $user->updateFreelancerScore();

        return $user;
    }

    /**
     * @return mixed
     */
    public function getEmailAllUser()
    {
        return $this->model->select('email')->get();
    }

    /**
     * @param string $escrowEmail
     * @return bool
     */
    public function addEscrowEmail(string $escrowEmail)
    {
        /** @var \App\Domains\Auth\Models\User */
        $user = auth()->user();

        throw_if(
            !$user = $user->update([
                'escrow_email' => $escrowEmail
            ]),
            Exception::class,
            'Add Escrow email failed.',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

        return $user;
    }

    /**
     * @param int $id
     * @param mixed $type
     * @return \App\Domains\Auth\Models\User
     */
    public function getForEdit(int $id, $type)
    {
        $user = $this->model->where([
            'id' => $id,
            'type' => $type
        ])
            ->first();

        return $user;
    }

    /**
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function deleteEmployer(int $id)
    {
        $user = $this->model->where([
            'id' => $id,
            'type' => User::TYPE_EMPLOYER
        ])
            ->with('jobs:id,user_id')
            ->first();

        throw_if(!$user, Exception::class, 'User not found.', Response::HTTP_NOT_FOUND);

        if ($this->jobService->deleteMultiJob($user->jobs->pluck('id')->toArray())) {
            $user->delete();

            return true;
        }

        return false;
    }

    /**
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function deleteFreelancer(int $id)
    {
        $freelancer = $this->model->where([
            'id' => $id,
            'type' => User::TYPE_FREELANCER
        ])->first();

        throw_if(!$freelancer, Exception::class, 'User not found.', Response::HTTP_NOT_FOUND);

        $freelancer->delete();

        return true;
    }

    /**
     * Get the specified model record from the database.
     *
     * @param $id
     * @param bool $withOnlineStatus
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById($id, bool $withOnlineStatus = false)
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();
        $this->query->select('*')->userNotHidden();

        if ($withOnlineStatus) {
            $usersOnlineId = $this->getUsersOnlineId();
            $this->query->selectOnlineStatus($usersOnlineId);
        }

        return $this->query->findOrFail($id);
    }

    /**
     * @param array $attempt
     * @param array $attributes
     * @return User|bool
     */
    public function firstAndUpdate(array $attempt, array $attributes)
    {
        $user = $this->model->where($attempt)->first();

        if (!$user) {
            return false;
        }
        $user->update($attributes);

        $user->updateFreelancerScore();

        return $user;
    }

    /**
     * Get users verified, not hidden.
     *
     * @param Request $request
     * @param array $columns
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getNormalUserForSelect2(Request $request, array $columns = ['*'])
    {
        $name = escapeLike($request->name);
        $oldValues = $request->query('old_values', []);

        $this->newQuery();

        return $this->query
            ->select($columns)
            ->whereIn('type', [User::TYPE_FREELANCER, User::TYPE_EMPLOYER])
            ->where('is_hidden', false)
            ->whereNotNull('email_verified_at')
            ->when($name, function ($e) use ($name) {
                $e->where('name', 'like', "$name%");
            })
            ->when(count($oldValues), function ($e) use ($oldValues) {
                $e->orderByRaw('(SELECT IF(u.id IN('. implode(',', $oldValues) .'), 1, 0) FROM users u WHERE u.id = users.id) DESC');
            })
            ->orderByRaw('(SELECT UPPER(LEFT(u.name, 1)) FROM users u WHERE u.id = users.id) ASC')
            ->paginate(30);
    }
}
