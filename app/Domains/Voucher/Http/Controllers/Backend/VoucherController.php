<?php

namespace App\Domains\Voucher\Http\Controllers\Backend;

use App\Domains\Voucher\Http\Request\Backend\StoreVoucherRequest;
use App\Domains\Voucher\Http\Request\Backend\UpdateStatusRequest;
use App\Domains\Voucher\Models\Voucher;
use App\Domains\Voucher\Services\VoucherService;
use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;

class VoucherController extends Controller
{
    /** @var VoucherService */
    protected $voucherService;

    /**
     *
     * @param VoucherService $voucherService
     */
    function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function index(Request $request)
    {
        $vouchers = $this->voucherService->list($request);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'List vouchers',
                'html' => view('backend.voucher.table', compact('vouchers'))->render(),
                'count' => $vouchers->total()
            ], Response::HTTP_OK);
        }

        return view('backend.voucher.index', compact('vouchers'));
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $maxDescription = Voucher::MAX_DESCRIPTION;
        $types = Voucher::getTypesName();

        return view('backend.voucher.create', compact('maxDescription', 'types'));
    }

    /**
     * @param StoreVoucherRequest $request
     * @return void
     */
    public function store(StoreVoucherRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->voucherService->store($request);
            DB::commit();

            return redirect()->route('admin.vouchers.index')->with('message', __('Create voucher success.'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());

            return redirect()->back()->withInput()->with('error', __('Create voucher failed.'));
        }
    }

    /**
     * @param int $id
     * @param UpdateStatusRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function updateStatus($id, UpdateStatusRequest $request)
    {
        if ($this->voucherService->updateStatus($id, $request->status)) {
            return response()->json([
                'message' => __('Update status success.'),
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => __('Update status failed.'),
                'error' => true
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
