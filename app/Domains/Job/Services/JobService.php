<?php

namespace App\Domains\Job\Services;

use App\Domains\Job\Events\NewJobCreated;
use App\Domains\Job\Models\Job;
use App\Domains\JobApplication\Models\JobApplication;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domains\Portfolio\Services\PortfolioService;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\Transaction\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Throwable;

/**
 * Class JobService.
 */
class JobService extends BaseService
{

    /**
     * @var PortfolioService
     */
    protected PortfolioService $portfolioService;

    /**
     * @var TransactionService
     */
    protected TransactionService $transactionService;

    /**
     * @param Job $job
     * @param PortfolioService $portfolioService
     * @param TransactionService $transactionService
     */
    public function __construct(Job $job, PortfolioService $portfolioService, TransactionService $transactionService)
    {
        $this->model = $job;
        $this->portfolioService = $portfolioService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param $request
     * @param $perPage
     * @return mixed
     */
    public function search($request, $perPage = false, $userId = false)
    {
        $query = $this->model::search($request)->get();

        if (is_numeric($perPage)) {
            $query = $this->model::search($request)->paginate($perPage);
        }

        if ($userId) {
            $query = $this->model::search($request)->where('user_id', $userId)->paginate($perPage);
        }

        return $query;
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function delete($id)
    {
        $job = $this->findJobDelete($id);
        $transactions = $job->transactions;
        throw_if(!$job, Exception::class, 'Job not found.', Response::HTTP_NOT_FOUND);
        $this->deleteFile($id);
        $job->delete();


        if ($transactions->isNotEmpty()) {
            $this->transactionService->cancelTransactionWhenJobDeleted($id, ...$transactions->pluck('receiver_id')->toArray());
        }

        return false;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws GeneralException
     * @throws Throwable
     */
    public function store(array $data = [])
    {
        DB::beginTransaction();
        try {
            $data['category'] = implode(",", $data['category_id']);
            $job = $this->model->create($data);
            $job->categories()->attach($data['category_id']);
            $data['file_upload'] = $data['file_upload'] ?? [];
            $this->portfolioService->setImagePortfolio($job->id, $data, false);
            DB::commit();
            $this->triggerEventSendMail($job);

            return $job;
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem creating this job. Please try again.'));
        }
    }

    /**
     * @param $params
     * @param $id
     * @return bool
     * @throws GeneralException|Throwable
     */
    public function update($params, $id)
    {
        DB::beginTransaction();
        try {
            $params['category'] = implode(",", $params['category_id']);
            $job = $this->getById($id);
            $isSendMail = $params['status'] != $job->status && !$job->has_sended_mail;
            $job->update($params);
            $jobInfo = $this->getById($id);
            $jobInfo->categories()->sync($params['category_id']);
            $this->portfolioService->updatePortfolio($params, $id, TYPE_JOB);
            $params['file_name'] = $params['file_name'] ?? [];
            $this->portfolioService->updateFileName($id, $params['file_name'], TYPE_JOB);
            DB::commit();
            if ($isSendMail) {
                $this->triggerEventSendMail($job);
            }
            return $job;
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem editing this job. Please try again.'));
        }
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getDataExport($request)
    {
        return $this->model::search($request)->get();
    }

    /**
     * @param $request
     * @param $userId
     * @param false $perPage
     * @return mixed
     */
    public function getByUserId($request, $userId, $perPage = false)
    {
        $request['userId'] = $userId;
        if (is_numeric($perPage)) {
            return $this->model::search($request)->paginate($perPage);
        }
        return $this->model::search($request)->get();
    }

    /**
     * @param $type
     * @param $job
     * @param $user
     * @param $request
     * @return JsonResponse|mixed|null
     */
    public function getJobPreview($type, $job, $user, $request)
    {
        try {
            $userCategories = $user->categories->pluck('id')->toArray();
            $userCategories = implode(',', $userCategories);
            $query = $this->model->where('status', Job::STATUS_OPEN)
                ->when($request->isEmployer, function ($query) {
                    $query->where('user_id', auth()->id());
                });
            if ($request->text) {
                $query = $query
                    ->where('jobs.name', 'like', '%' . $request->text . '%')
                    ->orWhere('jobs.description', 'like', '%' . $request->text . '%');
            }
            if ($request->sector_id) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('users.sector_id', $request->sector_id);
                });
            }
            if ($request->category_id) {
                $query->whereHas('categories', function ($query) use ($request) {
                    $query->where('categories.id', $request->category_id);
                });
            }
            if ($request->experience_id) {
                $query = $query->where('jobs.experience_id', $request->experience_id);
            }
            if ($request->country_id) {
                $query = $query->where('jobs.country_id', $request->country_id);
            }
            if ($request->orderBy) {
                $query = $query->orderBy('status', $request->orderBy);
            } else {
                $query = $query->orderBy(DB::raw(
                    '(CASE
                        WHEN category like ' . '"%' . $userCategories . '%"' . ' THEN 3
                        WHEN experience_id like ' . ($user->experience ? $user->experience->id : "null") . ' THEN 2
                        WHEN country_id like ' . ($user->country ? $user->country->id : "null") . ' THEN 1
                        ELSE 0
                        END)'
                ), 'DESC');
            }
            $idJobArray = $query->orderBy('id', 'DESC')->get()->pluck('id')->toArray();
            $jobIndex = array_search($job->id, $idJobArray);
            $result = null;
            if ($type == NEXT) {
                if ($jobIndex == count($idJobArray) - 1) {
                    $result = null;
                } else {
                    $result = $idJobArray[$jobIndex + 1];
                }
            }
            if ($type == PREVIOUS) {
                if ($jobIndex == 0) {
                    $result = null;
                } else {
                    $result = $idJobArray[$jobIndex - 1];
                }
            }

            return $result;
        } catch (Exception $e) {
            return response()->json(['error' => 'There was a problem this job. Please try again.']);
        }
    }

    /**
     * @param $job
     * @param $request
     * @param $user
     * @return JsonResponse|int
     */
    public function getPage($job, $request, $user)
    {
        try {
            $orderBy = $request->orderBy;
            $userCategories = $user->categories->pluck('id')->toArray();
            $userCategories = implode(',', $userCategories);
            $query = $this->model->select('id');

            if ($request->text) {
                $query = $query
                    ->where('jobs.name', 'like', '%' . $request->text . '%')
                    ->orWhere('jobs.description', 'like', '%' . $request->text . '%');
            }
            if ($request->sector_id) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('users.sector_id', $request->sector_id);
                });
            }
            if ($request->category_id) {
                $query->whereHas('categories', function ($query) use ($request) {
                    $query->where('categories.id', $request->category_id);
                });
            }
            if ($request->experience_id) {
                $query = $query->where('jobs.experience_id', $request->experience_id);
            }
            if ($request->country_id) {
                $query = $query->where('jobs.country_id', $request->country_id);
            }

            if ($orderBy) {
                $query = $query->orderBy('status', $orderBy);
            } else {
                $query = $query->orderBy(DB::raw(
                    '(CASE
                        WHEN category like ' . '"%' . $userCategories . '%"' . ' THEN 3
                        WHEN experience_id like ' . ($user->experience ? $user->experience->id : "null") . ' THEN 2
                        WHEN country_id like ' . ($user->country ? $user->country->id : "null") . ' THEN 1
                        ELSE 0
                        END)'
                ), 'DESC');
            }
            $jobs = $query->orderBy('id', TYPE_SORT_DESC)->get();
            $indexJob = array_search($job->id, $jobs->pluck('id')->toArray());

            return (int)($indexJob / config('paging.quantity')) + 1;
        } catch (Exception $e) {
            return response()->json(['page' => FIRST_PAGE]);
        }
    }

    /**
     * @param $param
     * @param $perPage
     * @param $userInfo
     * @return LengthAwarePaginator
     */
    public function searchFreelancerJob($param, $perPage = false, $userInfo = null)
    {
        $userCategories = $userInfo->categories->pluck('id')->toArray();
        $userCategories = implode(',', $userCategories);

        $query = $this->model::query();
        $query->with(['company', 'user', 'experience', 'country', 'timezone', 'categories']);

        if ($param['text']) {
            $query = $query
                ->where('jobs.name', 'like', '%' . $param['text'] . '%')
                ->orWhere('jobs.description', 'like', '%' . $param['text'] . '%');
        }
        if ($param['sector_id']) {
            $query->whereHas('user', function ($query) use ($param) {
                $query->where('users.sector_id', $param['sector_id']);
            });
        }
        if ($param['category_id']) {
            $query->whereHas('categories', function ($query) use ($param) {
                $query->where('categories.id', $param['category_id']);
            });
        }
        if ($param['experience_id']) {
            $query = $query->where('jobs.experience_id', $param['experience_id']);
        }
        if ($param['country_id']) {
            $query = $query->where('jobs.country_id', $param['country_id']);
        }


        if (!empty($param['orderBy'])) {
            $query->orderBy('status', $param['orderBy']);
        } else {
            $query->orderBy(DB::raw(
                '(CASE
                        WHEN jobs.category like ' . '"%' . $userCategories . '%"' . ' THEN 3
                        WHEN jobs.experience_id like ' . ($userInfo->experience ? $userInfo->experience->id : "null") . ' THEN 2
                        WHEN jobs.country_id like ' . ($userInfo->country ? $userInfo->country->id : "null") . ' THEN 1
                        ELSE 0
                        END)'
            ), 'DESC');
        }
        $query = $query->orderBy('id', TYPE_SORT_DESC);

        return $query->paginate($perPage);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getJobOpenById($id)
    {
        return $this->model::hasOpen()->where('id', $id)->first();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Throwable
     */
    public function createByEmployer(Request $request)
    {
        $inputs = $this->getInputs($request);
        $inputs['category'] = implode(",", $inputs['category_id']);
        $job = $this->model->create($inputs);
        $job->categories()->attach($inputs['category_id']);

        $data = [];
        $data['file_upload'] = $request->file('file_upload', []);
        $data['file_name'] = $request['file_name'] ?? [];

        throw_if(
            !$job ||
                (count($request->file('file_upload', [])) &&
                    !$this->portfolioService->setImagePortfolio(
                        $job->id,
                        $data,
                        false
                    )),
            Exception::class,
            'Create job failed.',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

        $this->triggerEventSendMail($job);

        return $job;
    }

    /**
     * @param int $userId
     * @param Request $request
     * @param int $paginateCount
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getMyJobApplied(int $userId, Request $request, int $paginateCount)
    {
        $this->newQuery()
            ->selectInfoJobApplication()
            ->joinToApplication($userId, JobApplication::STATUS_PENDING, JobApplication::STATUS_ESCROW_HANDLING)
            ->filter($request);

        $result = $this->query->paginate($paginateCount);

        $this->unsetClauses();

        return $result;
    }

    /**
     * @param int $userId
     * @return mixed
     * @throws Throwable
     */
    public function getAllMyJobApplied(int $userId)
    {
        return $this->model
            ->jobApplicationDefaultQuery($userId)
            ->pluck(JOBS_TABLE_NAME . '.id')
            ->toArray();
    }

    /**
     * @param int $id
     * @param int $userId
     * @return Job
     * @throws Throwable
     */
    public function getJobApplicationById(int $id, int $userId)
    {
        return $this->findJobApplicationByStatus($id, $userId, JobApplication::STATUS_PENDING, JobApplication::STATUS_ESCROW_HANDLING);
    }

    /**
     * Get inputs value in employer generated form.
     *
     * @param Request $request
     * @return array
     */
    protected function getInputs(Request $request)
    {
        /** @var \App\Domains\Auth\Models\User */
        $user = auth()->user();

        return array_merge(
            $request->only(
                [
                    'name',
                    'timezone_id',
                    'country_id',
                    'description',
                    'wage',
                    'experience_id',
                    'category_id'
                ]
            ),
            [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'status' => $request->boolean('status')
            ]
        );
    }

    /**
     * @param string $type
     * @param int $jobId
     * @param int|null $userId
     * @param Request|null $request
     * @return mixed|null
     */
    public function getJobApplicationPreview(string $type, int $jobId, $userId = null,  $request = null)
    {
        return $this->getJobApplicationPreviewByStatus($type, $jobId, $userId, $request, JobApplication::STATUS_PENDING, JobApplication::STATUS_ESCROW_HANDLING);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getJobWithUser($id)
    {
        return $this->model->where('user_id', Auth::user()->id)
            ->where('id', $id)
            ->whereNull('mark_done')
            ->with('applicants', function ($e) {
                $e->whereIn('job_applications.status', [JobApplication::STATUS_APPROVE, JobApplication::STATUS_ESCROW_HANDLING]);
            })
            ->first();
    }

    /**
     * @param int $id
     * @return bool|mixed
     */
    public function deleteByEmployer(int $id)
    {
        $job = $this->getJobWithUser($id);
        throw_if(!$job, Exception::class, 'Delete job failed.', Response::HTTP_NOT_FOUND);

        $applicants = $job->applicants->pluck('id')->toArray();
        $this->deleteFile($id);
        $job->delete();
        if (count($applicants)) {
            $this->transactionService->cancelTransactionWhenJobDeleted($job->id, ...$applicants);
        }

        return true;
    }

    /**
     * @return string
     */
    public function getPreviousUrl()
    {
        $previousRouteName = getPreviousRouteName();
        $previousRoute = url()->previous(EMPLOYER_INDEX);

        if (
            !$previousRouteName ||
            $previousRouteName === EMPLOYER_CREATE_JOB ||
            $previousRouteName === FRONTEND_LOGIN
        ) {
            $previousRoute = route(EMPLOYER_INDEX);
        }

        return $previousRoute;
    }

    /**
     * @param string|null $statusOrder
     * @param int $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function all($statusOrder = 'DESC', int $paginate = 20)
    {
        $this->newQuery()->selectInfoCommon($statusOrder);

        $result = $this->query->paginate($paginate);

        $this->unsetClauses();

        return $result;
    }

    /**
     * @param int $id
     * @return \App\Domains\Job\Models\Job
     */
    public function findWithNotAuthor(int $id)
    {
        $job = $this->model->select([
            'id',
            'name',
            'user_id',
            'country_id',
            'company_id',
            'experience_id',
            'timezone_id',
            'status',
            'description'
        ])
            ->where('id', $id)
            ->where('user_id', '!=', auth()->id())
            ->hasOpen()
            ->with('company:id,name,logo')
            ->with('user:id,name')
            ->with('categories:id,name,class')
            ->with('country:id,name,code')
            ->with('experience:id,name')
            ->first();

        throw_if(!$job, Exception::class, 'Job not found.', Response::HTTP_NOT_FOUND);

        return $job;
    }

    /**
     * @param string $type
     * @param int $id
     * @return mixed
     */
    public function getPreviewJobNotAuthor(string $type, int $id)
    {
        $result = null;
        $query = $this->model->newQuery()
            ->hasOpen()
            ->where('user_id', '!=', auth()->id())
            ->orderBy('id', TYPE_SORT_DESC)
            ->orderBy('created_at', TYPE_SORT_DESC);

        if ($type == NEXT) {
            $result = $query->where('id', '<', $id)->max('id');
        } else {
            $result = $query->where('id', '>', $id)->min('id');
        }

        return $result;
    }

    /**
     * @param string|null $statusOrder
     * @param int $paginate
     * @param bool $isRefreshPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listJobsSaved(Request $request, int $paginate = 20, bool $isRefreshPage = false)
    {
        $this->newQuery()->selectInfoCommon($request->query('orderBy'))->filter($request);

        $this->query
            ->whereHas('savers', function ($e) {
                $e->where('user_id', auth()->id());
            });

        $result = $this->query->paginate($paginate);

        if ((int) $request->page > $result->lastPage() && $isRefreshPage) {
            $request->instance()->query->set('page', $result->lastPage());
            return $this->listJobsSaved($request, $paginate);
        }

        $this->unsetClauses();

        return $result;
    }

    /**
     * @param string $statusOrder
     * @return $this
     */
    public function selectInfoCommon($statusOrder = 'DESC')
    {
        $this->query->select([
            'id',
            'name',
            'user_id',
            'country_id',
            'company_id',
            'experience_id',
            'timezone_id',
            'status',
            'mark_done',
            'description'
        ])
            ->with('company:id,name,logo')
            ->with('user:id,name')
            ->with('categories:id,name,class')
            ->with('country:id,name,code')
            ->with('experience:id,name')
            ->with('mySaved')
            ->orderBy('status', $this->handleOrderBy($statusOrder))
            ->orderBy('id', TYPE_SORT_DESC)
            ->orderBy('created_at', TYPE_SORT_DESC);

        return $this;
    }

    /**
     * @param $type
     * @param $job
     * @param $request
     * @return mixed
     */
    public function getJobPreviewOfEmployer($type, $job, $request)
    {
        $jobOpen = $this->model->where('status', Job::STATUS_OPEN)
            ->where('user_id', auth()->id())
            ->when($request->orderBy, function ($query) use ($request) {
                $query->orderBy('status', $request->orderBy);
            })->orderBy('id', 'DESC');

        if ($type == PREVIOUS) {
            return $jobOpen->where('id', '>', $job->id)->min('id');
        }
        return $jobOpen->where('id', '<', $job->id)->max('id');
    }

    /**
     * @param $request
     * @return int
     */
    public function getCurrentPageJob($request)
    {
        $request['userId'] = auth()->id();
        $jobs = $this->model::search($request)->select('id')->get();
        $indexJob = array_search($request->id, $jobs->pluck('id')->toArray());
        return (int)($indexJob / config('paging.quantity')) + 1;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function listFreelancerJob($request)
    {
        return $this->model->where('jobs.id', $request->id)
            ->select([
                'users.name as name',
                'users.avatar as avatar',
                'jobs.description as description',
                'job_applications.created_at as date_apply',
                'job_applications.id as id',
                'users.id as user_id',
                'job_applications.status as status'
            ])
            ->join('job_applications', function ($query) {
                $query->on('jobs.id', '=', 'job_applications.job_id');
            })
            ->join('users', function ($query) {
                $query->on('job_applications.user_id', '=', 'users.id');
            })->when($request->orderBy, function ($query) use ($request) {
                $query->orderBy('job_applications.created_at', $request->orderBy);
            })
            ->get();
    }

    /**
     * @param int $id
     * @param Request|null $request
     * @return \App\Domains\Job\Models\Job
     */
    public function findJobSaved(int $id, $request = null)
    {
        $this->newQuery()->selectInfoCommon();
        if ($request instanceof Request) {
            $this->filter($request);
        }

        $job = $this->query
            ->where('id', $id)
            ->hasOpen()
            ->whereHas('savers', function ($e) {
                $e->where('user_id', auth()->id());
            })
            ->first();
        $this->unsetClauses();

        throw_if(!$job, Exception::class, 'Job not found.', Response::HTTP_NOT_FOUND);

        return $job;
    }

    /**
     * @param string $type
     * @param int $id
     * @param Request|null $request
     * @return mixed
     */
    public function getPreviewJobSaved(string $type, int $id, $request = null)
    {
        $this->newQuery();
        if ($request instanceof Request) {
            $this->filter($request);
        }

        $result = null;

        $this->query
            ->hasOpen()
            ->where('user_id', '!=', auth()->id())
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
     * @param Request $request
     * @return $this
     */
    protected function filter(Request $request)
    {
        if (!is_null($request->query('hot_search'))) {
            $this->searchByKeyword($request->query('hot_search'));
        } else {
            $this->searchByKeyword($request->query('keyword'));
            $this->searchByKeyword($request->query('key_search'));
            $this->query
                ->when($request->query('sector_id'), function ($e, $sectorId) {
                    $e->whereHas('user.sector', function ($sub)  use ($sectorId) {
                        $sub->where('sectors.id', $sectorId);
                    });
                })
                ->when($request->query('experience_id'), function ($e, $experienceId) {
                    $e->whereHas('experience', function ($sub)  use ($experienceId) {
                        $sub->where('experiences.id', $experienceId);
                    });
                })
                ->when($request->query('country_id'), function ($e, $countryId) {
                    $e->whereHas('country', function ($sub)  use ($countryId) {
                        $sub->where('countries.id', $countryId);
                    });
                })
                ->when($request->query('category_id') && count($request->query('category_id', [])), function ($e) use ($request) {
                    $e->whereHas('categories', function ($sub)  use ($request) {
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
                $e->where('name', 'like', "%$keyword%")
                    ->orWhereHas('user', function ($sub)  use ($keyword) {
                        $sub->where('name', 'like', "%$keyword%");
                    })
                    ->orWhereHas('company', function ($sub)  use ($keyword) {
                        $sub->where('name', 'like', "%$keyword%");
                    });
            });
        }

        return $this;
    }

    /**
     * @param string|null $order
     * @return $this
     */
    protected function selectInfoJobApplication($order = null)
    {
        $this->query->select([
            JOBS_TABLE_NAME . '.id',
            JOBS_TABLE_NAME . '.name',
            JOBS_TABLE_NAME . '.user_id',
            JOBS_TABLE_NAME . '.country_id',
            JOBS_TABLE_NAME . '.company_id',
            JOBS_TABLE_NAME . '.experience_id',
            JOBS_TABLE_NAME . '.timezone_id',
            JOBS_TABLE_NAME . '.status',
            JOBS_TABLE_NAME . '.description',
            JOB_APPLICATIONS_TABLE_NAME . '.status as job_application_status',
            JOB_APPLICATIONS_TABLE_NAME . '.id as job_application_id'
        ])
            ->with('company:id,name,logo')
            ->with('user:id,name')
            ->with('categories:id,name,class')
            ->with('country:id,name,code')
            ->with('experience:id,name')
            ->with('timezone:id,offset,diff_from_gtm')
            ->with('mySaved')
            ->when($order, function ($e) use ($order) {
                $e->orderBy('job_application_status', $this->handleOrderBy($order));
            })
            ->orderBy('job_application_id', 'DESC');

        return $this;
    }

    /**
     * @param int $userId
     * @param int|string $status
     * @return $this
     */
    protected function joinToApplication(int $userId, ...$status)
    {
        $this->query
            ->join(JOB_APPLICATIONS_TABLE_NAME, function ($join) use ($userId, $status) {
                $join->on(JOB_APPLICATIONS_TABLE_NAME . '.job_id', '=', JOBS_TABLE_NAME . '.id')
                    ->where(JOB_APPLICATIONS_TABLE_NAME . '.user_id', $userId)
                    ->whereIn(JOB_APPLICATIONS_TABLE_NAME . '.status', $status);
            });

        return $this;
    }

    /**
     * @param int $userId
     * @param Request $request
     * @param int $paginateCount
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getMyJobDone(int $userId, Request $request, int $paginateCount)
    {
        $this->newQuery()
            ->selectInfoJobApplication($request->query('orderBy'))
            ->joinToApplication($userId, JobApplication::STATUS_DONE)
            ->filter($request);

        $result = $this->query->paginate($paginateCount);

        $this->unsetClauses();

        return $result;
    }

    /**
     * @param int $id
     * @param int $userId
     * @param mixed $status
     * @return Job
     * @throws Throwable
     */
    public function findJobApplicationByStatus(int $id, int $userId, ...$status)
    {
        $this->newQuery()
            ->selectInfoJobApplication(null)
            ->joinToApplication($userId, ...$status);

        $job = $this->query
            ->where(JOBS_TABLE_NAME . '.id', $id)
            ->first();

        $this->unsetClauses();

        throw_if(!$job, Exception::class, 'Job not found.', Response::HTTP_NOT_FOUND);

        return $job;
    }

    /**
     * @param string $type
     * @param int $jobId
     * @param int|null $userId
     * @param Request|null $request
     * @param mixed $status
     * @return mixed|null
     */
    public function getJobApplicationPreviewByStatus(string $type, int $jobId, $userId = null, $request = null, ...$status)
    {
        $this->newQuery()->joinToApplication($userId, ...$status);
        if ($request instanceof Request) {
            $this->filter($request);
        }

        $result = null;

        $this->query
            ->select([
                JOBS_TABLE_NAME . '.id',
                JOB_APPLICATIONS_TABLE_NAME . '.status as job_application_status',
                JOB_APPLICATIONS_TABLE_NAME . '.id as job_application_id'
            ])
            ->hasOpen()
            ->orderBy(JOBS_TABLE_NAME . '.id', TYPE_SORT_DESC)
            ->orderBy('job_application_id', 'DESC');

        if ($type == PREVIOUS) {
            $result = $this->query->where(JOBS_TABLE_NAME . '.id', '>', $jobId)->min(JOBS_TABLE_NAME . '.id');
        } else {
            $result = $this->query->where(JOBS_TABLE_NAME . '.id', '<', $jobId)->max(JOBS_TABLE_NAME . '.id');
        }

        return $result;
    }

    /**
     * @param int $id
     * @param int $userId
     * @return Job
     * @throws Throwable
     */
    public function findJobDone(int $id, int $userId)
    {
        return $this->findJobApplicationByStatus($id, $userId, JobApplication::STATUS_DONE);
    }

    /**
     * @param string $type
     * @param int $jobId
     * @param int|null $userId
     * @param Request|null $request
     * @return mixed|null
     */
    public function getJobDonePreview(string $type, int $jobId, $userId = null,  $request = null)
    {
        return $this->getJobApplicationPreviewByStatus($type, $jobId, $userId, $request, JobApplication::STATUS_DONE);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function markDoneJob($id)
    {
        return $this->model->where('id', $id)->first()->update([
            'mark_done' => Job::MARK_DONE,
            'due_date' => now()
        ]);
    }

    /**
     * @return int
     */
    public function getCountMyJob()
    {
        return $this->model->where('user_id', auth()->id())->count();
    }

    /**
     * @param int $id
     * @return \App\Domains\Job\Models\Job
     */
    public function findMyJobOpen(int $id)
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();

        $job = $this->query->where([
            'id' => $id,
            'user_id' => auth()->id(),
        ])->hasOpen()->first();

        return $job;
    }

    /**
     * @param $param
     * @param $perPage
     * @param $userInfo
     * @return int
     */
    public function getCountSearchJob($param, $perPage = false, $userInfo = null)
    {
        $userCategories = $userInfo->categories->pluck('id')->toArray();
        $userCategories = implode(',', $userCategories);

        $query = $this->model::query();
        $query->with(['company', 'user', 'experience', 'country', 'timezone', 'categories']);

        if ($param['text']) {
            $query = $query
                ->where('jobs.name', 'like', '%' . $param['text'] . '%')
                ->orWhere('jobs.description', 'like', '%' . $param['text'] . '%');
        }
        if ($param['sector_id']) {
            $query->whereHas('user', function ($query) use ($param) {
                $query->where('users.sector_id', $param['sector_id']);
            });
        }
        if ($param['category_id']) {
            $query->whereHas('categories', function ($query) use ($param) {
                $query->where('categories.id', $param['category_id']);
            });
        }
        if ($param['experience_id']) {
            $query = $query->where('jobs.experience_id', $param['experience_id']);
        }
        if ($param['country_id']) {
            $query = $query->where('jobs.country_id', $param['country_id']);
        }


        if (!empty($param['orderBy'])) {
            $query->orderBy('status', $param['orderBy']);
        } else {
            $query->orderBy(DB::raw(
                '(CASE
                        WHEN jobs.category like ' . '"%' . $userCategories . '%"' . ' THEN 3
                        WHEN jobs.experience_id like ' . ($userInfo->experience ? $userInfo->experience->id : "null") . ' THEN 2
                        WHEN jobs.country_id like ' . ($userInfo->country ? $userInfo->country->id : "null") . ' THEN 1
                        ELSE 0
                        END)'
            ), 'DESC');
        }
        $query = $query->orderBy('id', TYPE_SORT_DESC);

        return $query->count();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findJobDelete(int $id)
    {
        return $this->model->where('id', $id)
            ->with('transactions', function ($e) {
                $e->whereNotIn('status', [
                    Transaction::COMPLETE,
                    Transaction::PAYMENT_DISBURSED,
                    Transaction::CANCELED
                ]);
            })
            ->first();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteFile(int $id)
    {
        $filesDelete = $this->portfolioService->getPhotoByJobId($id);
        $this->portfolioService->deleteFileInStorage($filesDelete);

        return true;
    }

    /**
     * @param int $id
     * @return \App\Domains\Job\Models\Job
     */
    public function getForEdit(int $id)
    {
        return $this->model->where('id', $id)->first();
    }

    /**
     * @param array $jobIds
     * @return bool
     */
    public function deleteMultiJob(array $jobIds)
    {
        $jobs = $this->model->whereIn('id', $jobIds)
            ->with('transactions', function ($e) {
                $e->whereNotIn('status', [
                    Transaction::COMPLETE,
                    Transaction::PAYMENT_DISBURSED,
                    Transaction::CANCELED
                ]);
            })
            ->get();

        foreach ($jobs as $job) {
            if ($job) {
                try {
                    $transactions = $job->transactions;
                    $this->deleteFile($job->id);
                    $job->delete();

                    if ($transactions->isNotEmpty()) {
                        $this->transactionService->cancelTransactionWhenJobDeleted($job->id, ...$transactions->pluck('receiver_id')->toArray());
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }

        return true;
    }

    /**
     * @param Job $job
     * @return void
     */
    public function triggerEventSendMail(Job $job)
    {
        try {
            if ($job->status == Job::STATUS_OPEN) {
                event(new NewJobCreated($job));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
