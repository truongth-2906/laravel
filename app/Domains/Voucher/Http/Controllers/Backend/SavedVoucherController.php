<?php

namespace App\Domains\Voucher\Http\Controllers\Backend;

use App\Domains\Voucher\Services\SavedVoucherService;
use App\Http\Controllers\Controller;

class SavedVoucherController extends Controller
{
    /** @var SavedVoucherService */
    protected $savedVoucherService;

    /**
     * @param SavedVoucherService $savedVoucherService
     */
    function __construct(SavedVoucherService $savedVoucherService)
    {
        $this->savedVoucherService = $savedVoucherService;
    }

    /**
     * @param int $voucherId
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|void
     */
    public function listByVoucherId($voucherId)
    {
        if (request()->wantsJson()) {
           $saves = $this->savedVoucherService->listByVoucherId($voucherId);

           return response()->json([
                'html' => view('backend.voucher.saved-row', compact('saves'))->render(),
                'has_next_page' => $saves->nextPageUrl() ? true : false
           ]);
        }
    }
}
