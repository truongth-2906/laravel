<?php

namespace App\Domains\Auth\Http\Controllers\Backend\Employer;

use App\Domains\Auth\Exports\EmployerExport;
use App\Domains\Auth\Http\Requests\Backend\Employer\UpdateEmployerRequest;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\EmployerService;
use App\Domains\Auth\Services\UserService;
use App\Domains\Company\Services\CompanyService;
use App\Domains\Portfolio\Services\PortfolioService;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Domains\Auth\Http\Requests\Backend\Employer\StoreEmployerRequest;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class EmployerController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @var EmployerService
     */
    protected EmployerService $employerService;

    /**
     * @var CompanyService
     */
    protected CompanyService $companyService;

    /**
     * @var PortfolioService
     */
    protected PortfolioService $portfolioService;

    /**
     * @param UserService $userService
     * @param CompanyService $companyService
     * @param EmployerService $employerService
     * @param PortfolioService $portfolioService
     */
    public function __construct(
        UserService $userService,
        CompanyService $companyService,
        EmployerService $employerService,
        PortfolioService $portfolioService
    ) {
        $this->userService = $userService;
        $this->companyService = $companyService;
        $this->employerService = $employerService;
        $this->portfolioService = $portfolioService;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $employers = $this->userService->search($request, User::TYPE_EMPLOYER, config('paging.quantity'));
        $orderByType = $request->orderByType ?? 'DESC';
        $orderByField = $request->orderByField ?? '';

        if ($request->ajax()) {
            $view = view('backend.employer.table', compact('employers', 'orderByType', 'orderByField'))->render();
            return response()->json([
                'html' => $view,
                'total' => $employers->total()
            ]);
        }
        return view('backend.employer.index', compact('employers', 'orderByType', 'orderByField'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $type = User::TYPE_EMPLOYER;
        $lengthBio = User::BIO_LENGTH;

        return view('backend.employer.create', compact('type', 'lengthBio'));
    }

    /**
     * @throws GeneralException
     */
    public function store(StoreEmployerRequest $request)
    {
        if ($this->employerService->store($request->all(), $this->companyService, $this->portfolioService)) {
            return redirect()->route('admin.employer.index')->with(
                'message',
                __('The employer was successfully created.')
            );
        }
        return redirect()->back()->withInput()->with('error', __('The employer create failed.'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function edit($id)
    {
        $employer = $this->userService->getForEdit($id, User::TYPE_EMPLOYER);
        if ($employer) {
            $lengthBio = User::BIO_LENGTH;

            return view('backend.employer.edit', compact('employer', 'lengthBio'));
        }

        return redirect()->back()->with('error', 'Employer not found.');
    }

    /**
     * @param UpdateEmployerRequest $request
     * @param $id
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function update(UpdateEmployerRequest $request, $id)
    {
        if ($this->employerService->update($request->all(), $id, $this->companyService, $this->portfolioService)) {
            return redirect()->route('admin.employer.index')->with(
                'message',
                __('The employer was successfully updated.')
            );
        }
        return redirect()->back()->withInput()->with('error', __('The employer update failed.'));
    }

    /**
     * @param $id
     * @return JsonResponse|RedirectResponse
     * @throws Throwable
     */
    public function delete($id)
    {
        try {
            $this->userService->deleteEmployer($id);

            session()->flash('message', __('Delete success'));

            if (request()->wantsJson()) {
                return response()->json(
                    [
                        'message' => __('Delete success'),
                        'url' => route('admin.employer.index')
                    ],
                    Response::HTTP_OK
                );
            }

            return redirect()->route('admin.employer.index');
        } catch (Exception $e) {
            session()->flash('message', __('Delete failed'));
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => $e->getCode() == Response::HTTP_NOT_FOUND ? __('No data employer') : __(
                        'Delete failed'
                    ),
                    'error' => true
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->route('admin.employer.index');
        }
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(Request $request)
    {
        return Excel::download(new EmployerExport($request), 'employers_' . now()->format('Y-m-d') . '.csv');
    }
}
