<?php

namespace App\Domains\Auth\Http\Controllers\Backend\Freelancer;

use App\Domains\Auth\Exports\FreelancerExport;
use App\Domains\Auth\Http\Requests\Backend\Freelancer\StoreFreelancerRequest;
use App\Domains\Auth\Http\Requests\Backend\Freelancer\UpdateFreelancerRequest;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\FreelancerService;
use App\Domains\Auth\Services\UserService;
use App\Domains\Category\Services\CategoryService;
use App\Domains\Country\Services\CountryService;
use App\Domains\Experience\Services\ExperienceService;
use App\Domains\Portfolio\Services\PortfolioService;
use App\Domains\Timezone\Services\TimezoneService;
use App\Exceptions\GeneralException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 *
 */
class FreelancerController
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @var CategoryService
     */
    protected CategoryService $categoryService;

    /**
     * @var CountryService
     */
    protected CountryService $countryService;

    /**
     * @var ExperienceService
     */
    protected ExperienceService $experienceService;

    /**
     * @var TimezoneService $timezoneService
     */
    protected TimezoneService $timezoneService;

    /**
     * @var PortfolioService
     */
    protected PortfolioService $portfolioService;

    /**
     * @var FreelancerService
     */
    protected FreelancerService $freelancerService;

    /**
     * FreelancerController constructor.
     * @param UserService $userService
     * @param CategoryService $categoryService
     * @param CountryService $countryService
     * @param ExperienceService $experienceService
     * @param TimezoneService $timezoneService
     * @param PortfolioService $portfolioService
     * @param FreelancerService $freelancerService
     */
    public function __construct(
        UserService $userService,
        CategoryService $categoryService,
        CountryService $countryService,
        ExperienceService $experienceService,
        TimezoneService $timezoneService,
        PortfolioService $portfolioService,
        FreelancerService $freelancerService
    ) {
        $this->userService = $userService;
        $this->categoryService = $categoryService;
        $this->countryService = $countryService;
        $this->experienceService = $experienceService;
        $this->timezoneService = $timezoneService;
        $this->portfolioService = $portfolioService;
        $this->freelancerService = $freelancerService;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $freelancers = $this->userService->search($request, User::TYPE_FREELANCER, config('paging.quantity'));
        $orderByType = $request->orderByType ?? 'DESC';
        $orderByField = $request->orderByField ?? '';

        if ($request->ajax()) {
            $view = view('backend.freelancer.table', compact('freelancers', 'orderByField', 'orderByType'))->render();
            return response()->json([
                'html' => $view,
                'total' => $freelancers->total()
            ]);
        }
        return view('backend.freelancer.index', compact('freelancers', 'orderByField', 'orderByType'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $type = User::TYPE_FREELANCER;
        $lengthBio = User::BIO_LENGTH;

        return view('backend.freelancer.create', compact('type', 'lengthBio'));
    }

    /**
     * @param StoreFreelancerRequest $request
     * @return mixed
     * @throws GeneralException
     * @throws Throwable
     */
    public function store(StoreFreelancerRequest $request)
    {
        if ($this->freelancerService->store($request->validated(), $this->portfolioService)) {
            return redirect()->route('admin.freelancer.index')->with(
                'message',
                __('The freelancer was successfully created.')
            );
        }

        return redirect()->back()->withInput()->with(
            'error',
            __('The freelancer create failed.')
        );
    }

    /**
     * @param $id
     * @return Application|Factory|View|\Illuminate\Routing\Redirector|RedirectResponse
     */
    public function edit($id)
    {
        $freelancer = $this->userService->getForEdit($id, User::TYPE_FREELANCER);
        if ($freelancer) {
            $lengthBio = User::BIO_LENGTH;

            return view('backend.freelancer.edit', compact('freelancer', 'lengthBio'));
        }
        return redirect()->back()->with('error', 'Freelancer not found.');
    }

    public function update(UpdateFreelancerRequest $request, $id)
    {
        if ($this->freelancerService->update($request->all(), $id, $this->portfolioService)) {
            return redirect()->route('admin.freelancer.index')->with(
                'message',
                __('The freelancer was successfully updated.')
            );
        }

        return redirect()->back()->withInput()->with(
            'error',
            __('The freelancer update failed.')
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function delete($id)
    {
        $this->userService->deleteFreelancer($id);

        session()->flash('message', __('Delete freelancer success'));

        return response()->json(
            [
                'message' => __('Delete freelancer success'),
                'url' => route('admin.freelancer.index')
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export(Request $request)
    {
        return Excel::download(new FreelancerExport($request), 'freelancers_' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * @param int $id
     * @param string $status
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function updateStatusHidden($id, $status)
    {
        try {
            throw_if(
                !$this->freelancerService->updateStatusHidden($id, $status),
                Exception::class,
                'Update failed.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

            return response()->json([
                'message' => __('Update status hidden success.'),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => __('Update status hidden failed.'),
                'error' => true
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
