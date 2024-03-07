<?php

namespace App\Domains\Voucher\Services;

use App\Domains\Voucher\Models\SavedVoucher;
use App\Services\BaseService;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SavedVoucherService extends BaseService
{
    /**
     * @param SavedVoucher $model
     */
    function __construct(SavedVoucher $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $voucherId
     * @return LengthAwarePaginator
     */
    public function listByVoucherId(int $voucherId): LengthAwarePaginator
    {
        $this->newQuery();

        return $this->query
            ->select(
                'saved_vouchers.*',
                'vouchers.number_times_used_type as number_times_used_type',
                'vouchers.number_times_used_value as number_times_used_value',
                'vouchers.expired_date as voucher_expired_date'
            )
            ->join('vouchers', 'vouchers.id', '=', 'saved_vouchers.voucher_id')
            ->where('saved_vouchers.voucher_id', $voucherId)
            ->where('saved_vouchers.status', SavedVoucher::STATUS_ACTIVATED)
            ->with('user:id,name')
            ->with('historiesUsed', 'historiesUsed.transaction:id,escrow_transaction_id')
            ->paginate(config('paging.quantity'));
    }

    /**
     * @param int $voucherId
     * @return mixed
     */
    public function lockForUpdateByVoucherId(int $voucherId)
    {
        return $this->model->where('voucher_id', $voucherId)->lockForUpdate()->get();
    }
}
