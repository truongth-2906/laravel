<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Events\User\UserCreated;
use App\Domains\Auth\Models\User;
use App\Domains\Job\Services\JobService;
use App\Domains\JobApplication\Models\JobApplication;
use App\Domains\Notification\Models\Notification;
use App\Domains\Notification\Services\NotificationService;
use App\Domains\Transaction\Events\UserHidden;
use App\Exceptions\GeneralException;
use App\Scopes\UserNotHiddenScope;
use App\Services\BaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class UserService.
 */
class FreelancerService extends BaseService
{
    /** @var JobService */
    protected $jobService;

    /** @var NotificationService */
    protected $notificationService;

    /**
     * UserService constructor.
     *
     * @param User $user
     * @param JobService $jobService
     */
    public function __construct(
        User $user,
        JobService $jobService,
        NotificationService $notificationService
    ) {
        $this->model = $user;
        $this->jobService = $jobService;
        $this->notificationService = $notificationService;
    }

    /**
     * @param array $data
     * @return User|mixed
     * @throws GeneralException
     * @throws Throwable
     */
    public function store(array $data = [], $portfolioService = null)
    {
        DB::beginTransaction();

        try {
            $data = $this->getParam($data);
            $user = $this->createUser($data);

            $user->syncRoles($data['roles'] ?? []);
            $user->categories()->attach($data['categories'] ?? []);
            if ($portfolioService !== null) {
                $portfolioService->setImagePortfolio($user->id, $data);
            }

            if (!config('base.access.user.only_roles')) {
                $user->syncPermissions($data['permissions'] ?? []);
            }

            $user->updateFreelancerScore();
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        event(new UserCreated($user));

        DB::commit();

        // They didn't want to auto verify the email, but do they want to send the confirmation email to do so?
        if (
            !isset($data['email_verified'])
            && isset($data['send_confirmation_email'])
            && $data['send_confirmation_email'] === $this->model::IS_ACTIVE
        ) {
            $user->sendEmailVerificationNotification();
        }

        return $user;
    }

    /**
     * @param array $data
     * @return User
     */
    protected function createUser(array $data = []): User
    {
        $data['avatar'] = $this->uploadAvatar($data['avatar']);

        return $this->model::create([
            'type' => $data['type'] ?? $this->model::TYPE_EMPLOYER,
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => $data['password'] ?? null,
            'provider' => $data['provider'] ?? null,
            'provider_id' => $data['provider_id'] ?? null,
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'active' => $data['active'] ?? false,
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'country_id' => $data['country_id'],
            'timezone_id' => $data['timezone_id'],
            'bio' => $data['bio'],
            'experience_id' => $data['experience_id'] ?? null,
            'avatar' => $data['avatar'],
            'phone_number' => $data['phone_number'],
            'calling_code_id' => $data['calling_code_id'],
            'tag_line' => $data['tag_line'],
        ]);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getParam(array $data = []): array
    {
        return [
            'type' => $data['type'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'name' => $data['firstname'] . ' ' . $data['lastname'],
            'country_id' => $data['country_id'],
            'timezone_id' => $data['timezone_id'],
            'bio' => $data['bio'],
            'experience_id' => $data['experience_id'],
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'active' => isset($data['active']) && $data['active'] === $this->model::IS_ACTIVE,
            'avatar' => $data['avatar'] ?? NULL,
            'file_upload' => $data['file_upload'] ?? [],
            'categories' => $data['categories'] ?? [],
            'portfolios_delete' => $data['portfolios_delete'] ?? [],
            'file_name' => $data['file_name'] ?? [],
            'phone_number' => $data['phone_number'],
            'calling_code_id' => $data['calling_code'],
            'tag_line' => $data['tag_line'],
        ];
    }

    /**
     * @param array $data
     * @param $id
     * @param $portfolioService
     * @return mixed|User
     */
    public function update(array $data = [], $id, $portfolioService = null)
    {
        DB::beginTransaction();

        try {
            $user = $this->getById($id);

            if (array_key_exists('avatar', $data)) {
                $data['avatar'] = $this->uploadAvatar($data['avatar']);
            } else {
                $data['avatar'] = $user->avatar;
            }
            $dataFormat = $this->getParam($data);
            if (is_null($data['password'])) {
                unset($dataFormat['password']);
            }
            $this->getById($id)->update($dataFormat);

            $user->categories()->sync($dataFormat['categories']);
            $portfolioService->updatePortfolio($dataFormat, $id, TYPE_USER);
            $portfolioService->updateFileName($id, $dataFormat['file_name'], TYPE_USER);

            $user->updateFreelancerScore();

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * @param $avatar
     * @return string|null
     */
    protected function uploadAvatar($avatar, $fileName = null): ?string
    {
        if ($avatar) {
            $fileName = (now()->timestamp) . '.' . $avatar->extension();
            $avatar->storeAs('/public/users', $fileName, 'azure');
        }
        return $fileName;
    }

    /**
     * @param array $data
     * @param $portfolioService
     * @return bool
     * @throws GeneralException
     */
    public function updateFreelancer($portfolioService, array $data = []): bool
    {
        DB::beginTransaction();

        try {
            $freelancer = auth()->user();
            $id = auth()->user()->id;

            if (array_key_exists('avatar', $data)) {
                $data['avatar'] = $this->uploadAvatar($data['avatar']);
            } else {
                $data['avatar'] = $freelancer->avatar;
            }
            $data = $this->formatData($data);

            $freelancer->update($data);
            $freelancer->categories()->sync($data['categories']);
            $freelancer->updateFreelancerScore();
            $portfolioService->updatePortfolio($data, $id, TYPE_USER);
            $portfolioService->updateFileName($id, $data['file_name'], TYPE_USER);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function formatData(array $data = []): array
    {
        return [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'name' => $data['firstname'] . ' ' . $data['lastname'],
            'country_id' => $data['country_id'],
            'timezone_id' => $data['timezone_id'],
            'experience_id' => $data['experience_id'],
            'bio' => $data['bio'],
            'categories' => $data['categories'] ?? [],
            'email' => $data['email'],
            'avatar' => $data['avatar'],
            'file_upload' => $data['file_upload'] ?? [],
            'portfolios_delete' => $data['portfolios_delete'] ?? [],
            'file_name' => $data['file_name'] ?? [],
            'phone_number' => $data['phone_number'],
            'calling_code_id' => $data['calling_code'],
            'tag_line' => $data['tag_line'],
        ];
    }

    /**
     * @param $data
     * @return bool
     * @throws GeneralException
     */
    public function apply($data)
    {
        /** @var \App\Domains\Auth\Models\User */
        $user = auth()->user();

        throw_if(
            !$user->isFreelancer() ||
                !array_key_exists('job_id', $data) ||
                !$job = $this->jobService->getJobOpenById($data['job_id']),
            Exception::class,
            Response::HTTP_NOT_FOUND,
            'Job not found.'
        );
        if (!$user->jobApplications()->wherePivot('job_id', $job->id)->exists()) {
            $user->jobApplications()->attach($job->id, ['status' => IS_APPLIED]);
            $this->notificationService->createByJob($job->load('user', 'applicants'), Notification::JOB_APPLY_TYPE);
        }

        return true;
    }

    /**
     * @param $data
     * @return bool
     * @throws GeneralException
     * @throws Throwable
     */
    public function settingAvailable($data)
    {
        DB::beginTransaction();
        try {
            $freelancer = $this->getById($data['id'])->update([
                'available' => $data['available'],
                'hours' => $data['hours'],
                'rate_per_hours' => $data['rate_per_hours']
            ]);
            DB::commit();
            return $freelancer;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem setting available. Please try again.'));
        }
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * @param $key
     * @param $data
     * @return mixed
     * @throws GeneralException
     * @throws Throwable
     */
    public function updateIdentifierPassbase($key, $data)
    {
        DB::beginTransaction();
        try {
            $freelancer = $this->getByEmail($data['owner']['email']);
            if ($freelancer) {
                $freelancer->update([
                    'identity_passbase' => $key
                ]);
            }
            DB::commit();
            return $freelancer;
        } catch (Exception $e) {
            throw new GeneralException(__('There was a problem update identifier passbase. Please try again.'));
        }
    }

    /**
     * @param $data
     * @return mixed
     * @throws GeneralException
     * @throws Throwable
     */
    public function verifiedPassbase($data)
    {
        DB::beginTransaction();
        try {
            $freelancer = $this->getByEmail($data['owner']['email']);
            if ($freelancer) {
                switch ($data['status']) {
                    case "approved":
                        $freelancer->update([
                            'active' => $this->model::IS_ACTIVE,
                        ]);
                        break;
                    case "declined":
                        $freelancer->update([
                            'active' => $this->model::IS_DECLINED,
                        ]);
                        break;
                    default:
                        $freelancer->update([
                            'active' => $this->model::IS_PENDING,
                        ]);
                }
            }
            DB::commit();
            return $freelancer;
        } catch (Exception $e) {
            throw new GeneralException(__('There was a problem verified. Please try again.'));
        }
    }

    /**
     * @param Request $request
     * @param int $paginate
     * @param bool $isRefreshPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listFreelancerSaved(Request $request, int $paginate = 20, bool $isRefreshPage = false)
    {
        $this->newQuery()->selectInfoCommon($request->query('orderBy'))->filter($request);

        $this->query
            ->freelancers()
            ->where('id', '!=', auth()->id())
            ->whereHas('savers', function ($e) {
                $e->where('user_id', auth()->id());
            });

        $result = $this->query->paginate($paginate);

        if ((int)$request->page > $result->lastPage() && $isRefreshPage) {
            $request->instance()->query->set('page', $result->lastPage());
            return $this->listFreelancerSaved($request, $paginate);
        }

        $this->unsetClauses();

        return $result;
    }

    /**
     * @param string $statusOrder
     * @return $this
     */
    protected function selectInfoCommon($statusOrder = 'DESC')
    {
        $this->query->select([
            'id',
            'name',
            'email',
            'avatar',
            'country_id',
            'experience_id',
            'available',
            'email_verified_at',
            'tag_line',
            'hours',
            'rate_per_hours'
        ])
            ->with('categories:id,name,class')
            ->with('country:id,name,code')
            ->with('experience:id,name')
            ->orderBy('available', $this->handleOrderBy($statusOrder))
            ->orderBy('id', TYPE_SORT_DESC)
            ->orderBy('created_at', TYPE_SORT_DESC);

        return $this;
    }

    /**
     * @param Request $request
     * @return $this
     */
    protected function filter(Request $request)
    {
        if (!is_null($request->query('hot_search'))) {
            $this->searchByKeyword($request->query('hot_search'));
        } else {
            $this->searchByKeyword($request->query('keyword'));
            $this->query
                ->when($request->query('experience_id'), function ($e, $experienceId) {
                    $e->whereHas('experience', function ($sub) use ($experienceId) {
                        $sub->where('experiences.id', $experienceId);
                    });
                })
                ->when($request->query('country_id'), function ($e, $countryId) {
                    $e->whereHas('country', function ($sub) use ($countryId) {
                        $sub->where('countries.id', $countryId);
                    });
                })
                ->when($request->query('category_id') && count($request->query('category_id', [])), function ($e) use ($request) {
                    $e->whereHas('categories', function ($sub) use ($request) {
                        $sub->whereIn('categories.id', $request->query('category_id'));
                    });
                });
        }

        return $this;
    }

    /**
     * @param string|null $keyword
     * @return $this
     */
    protected function searchByKeyword($keyword)
    {
        if (!is_null($keyword) && $keyword != '') {
            $keyword = escapeLike($keyword);
            $this->query->where(function ($e) use ($keyword) {
                $e->orWhere('name', 'like', "%$keyword%")
                    ->orWhere('email', 'like', "%$keyword%");
            });
        }

        return $this;
    }


    /**
     * @param int $id
     * @param Request|null $request
     * @return \App\Domains\Auth\Models\User
     */
    public function findFreelancerSaved(int $id, $request = null)
    {
        $this->newQuery()->selectInfoCommon();
        if ($request instanceof Request) {
            $this->filter($request);
        }

        $freelancer = $this->query
            ->where('id', $id)
            ->hasAvailable()
            ->freelancers()
            ->whereHas('savers', function ($e) {
                $e->where('user_id', auth()->id());
            })
            ->first();
        $this->unsetClauses();

        throw_if(!$freelancer, Exception::class, 'Freelancer not found.', Response::HTTP_NOT_FOUND);

        return $freelancer;
    }

    /**
     * @param string $type
     * @param int $id
     * @param Request|null $request
     * @return mixed
     */
    public function getPreviewSaved(string $type, int $id, $request = null)
    {
        $this->newQuery();
        if ($request instanceof Request) {
            $this->filter($request);
        }

        $result = null;

        $this->query
            ->hasAvailable()
            ->freelancers()
            ->whereHas('savers', function ($e) {
                $e->where('user_id', auth()->id());
            })
            ->orderBy('id', TYPE_SORT_DESC)
            ->orderBy('created_at', TYPE_SORT_DESC);

        if ($type == NEXT) {
            $result = $this->query->where('id', '<', $id)->max('id');
        } else {
            $result = $this->query->where('id', '>', $id)->min('id');
        }

        return $result;
    }

    /**
     * @param $request
     * @param $type
     * @return mixed
     */
    public function getDataExport($request, $type)
    {
        return $this->model::search($request, $type)->with('callingCode', 'country', 'utc', 'categories', 'experience')->get();
    }

    /**
     * @param int $id
     * @param string $status
     * @return mixed
     */
    public function updateStatusHidden(int $id, string $status)
    {
        $status = $status == User::HIDDEN;

        try {
            DB::beginTransaction();
            $freelancer = $this->model->where('id', $id)
                ->where('is_hidden', '!=', $status)
                ->freelancers()
                ->first();

            throw_if(!$freelancer, Exception::class, 'Freelancer not found.', Response::HTTP_NOT_FOUND);
            $freelancer->update(['is_hidden' => $status]);
            if ($status) {
                $freelancer->jobApplicationsDetail()
                    ->whereNotIn('status', [JobApplication::STATUS_REJECT, JobApplication::STATUS_DONE])
                    ->withoutGlobalScope(UserNotHiddenScope::class)
                    ->delete();
                    event(new UserHidden($freelancer));
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        return true;
    }

    /**
     * Get the specified model record from the database.
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById($id)
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();

        return $this->query->userNotHidden()->findOrFail($id);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getUserNotHidden()
    {
        $this->unsetClauses();
        $this->newQuery();

        return $this->query->where('is_hidden', false)->whereNotNull('email_verified_at')->freelancers()->get();
    }
}
