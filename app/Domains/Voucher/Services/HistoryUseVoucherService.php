<?php

namespace App\Domains\Voucher\Services;

use App\Domains\Voucher\Models\HistoryUseVoucher;
use App\Services\BaseService;

class HistoryUseVoucherService extends BaseService
{
    /**
     * @param HistoryUseVoucher $model
     */
    function __construct(HistoryUseVoucher $model)
    {
        $this->model = $model;
    }

    /**
     * @param array|Illuminate\Support\Collection $ids
     * @return mixed
     */
    public function lockForUpdateBySavedIds($ids)
    {
        return $this->model->whereIn('saved_voucher_id', $ids)->lockForUpdate()->get();
    }
}
