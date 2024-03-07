<?php

namespace App\Domains\Voucher\Services;

use App\Domains\Voucher\Models\SavedVoucher;
use App\Domains\Voucher\Models\Voucher;
use App\Services\BaseService;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Log;

class VoucherService extends BaseService
{
    /** @var SavedVoucherService */
    protected $savedVoucherService;

    /** @var HistoryUseVoucherService */
    protected $historyUseVoucherService;

    /**
     * @param Voucher $model
     * @param SavedVoucherService $savedVoucherService
     * @param HistoryUseVoucherService $historyUseVoucherService
     */
    function __construct(Voucher $model, SavedVoucherService $savedVoucherService, HistoryUseVoucherService $historyUseVoucherService)
    {
        $this->model = $model;
        $this->savedVoucherService = $savedVoucherService;
        $this->historyUseVoucherService = $historyUseVoucherService;
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function list(Request $request, array $columns = ['*']): LengthAwarePaginator
    {
        $this->newQuery();

        $this->query
            ->select($columns)
            ->addSelect(DB::raw($this->selectCountUsedQuery() . " AS count_used"));

        $this->handleSearch($request->query('search', ''))->handleSort($request->query('order_by_field', ''), $request->query('order_by_type', ''));

        return $this->query->orderBy('created_at', 'DESC')
            ->setBindings([SavedVoucher::STATUS_ACTIVATED], 'select')
            ->paginate(config('paging.quantity'));
    }

    /**
     * @param mixed $searchValue
     * @return $this
     */
    public function handleSearch($searchValue)
    {
        $searchValue = escapeLike($searchValue);
        $columnsLikeSearch = ['name', 'code', 'type'];
        $columnsEqualSearch = ['discount', 'count'];

        $this->query->when($searchValue, function ($e, $searchValue) use ($columnsLikeSearch, $columnsEqualSearch) {
            $e->where(function ($q) use ($searchValue, $columnsLikeSearch, $columnsEqualSearch)  {
                $q = $q->when(strtotime($searchValue) !== false, function ($e) use ($searchValue) {
                    $e->orWhereDate('expired_date', '=', date('Y-m-d', strtotime($searchValue)));
                });

                foreach ($columnsLikeSearch as $name) {
                    $q = $q->orWhere($name, 'like', "%$searchValue%");
                }

                foreach ($columnsEqualSearch as $name) {
                    $q = $q->orWhere($name, $searchValue);
                }
                if (is_numeric($searchValue)) {
                    $selectCountUsedQuery = $this->selectCountUsedQuery();
                    $q = $q->orWhereRaw("$selectCountUsedQuery = ?", [SavedVoucher::STATUS_ACTIVATED, $searchValue]);
                }
            });
        });

        return $this;
    }

    /**
     * @return $this
     */
    protected function handleSort(string $field, string $type)
    {
        $type = strtoupper($type);

        if ($this->validateDataSort($field, $type)) {
            $this->query->orderBy($field, $type);
        }

        return $this;
    }

    /**
     * @param string $field
     * @param string $type
     * @return bool
     */
    public function validateDataSort(string $field, string $type)
    {
        return in_array($field, Voucher::FIELDS_ALLOWED_SORT) && in_array($type, [TYPE_SORT_ASC, TYPE_SORT_DESC]);
    }

    /**
     * @return string
     */
    public function selectCountUsedQuery()
    {
        return '(SELECT COUNT(*) FROM saved_vouchers v INNER JOIN history_use_vouchers h ON v.id = h.saved_voucher_id WHERE v.voucher_id = vouchers.id AND v.status = ? AND v.deleted_at IS NULL AND h.deleted_at IS NULL)';
    }

    /**
     * @param Request $request
     * @return \App\Domains\Voucher\Models\Voucher
     */
    public function store(Request $request)
    {
        $attributes = $this->getFormData($request);
        $voucher = $this->model->create($attributes);
        if ($voucher instanceof Voucher && $voucher->scope == Voucher::SPECIFY_SCOPE) {
            $voucher->savers()->syncWithPivotValues($attributes['users_specify'], ['status' => SavedVoucher::STATUS_SPECIFY]);
        }

        return $voucher;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getFormData(Request $request)
    {
        $attributes = $request->only('name', 'description', 'discount', 'max_discount', 'count', 'expired_date', 'users_specify', 'number_times_used_value');
        $attributes['user_id'] = auth()->id();
        $attributes['code'] = $this->randomUniqueCode(Voucher::LENGTH_CODE);
        $attributes['type'] = $request->get('discount_type');
        $attributes['status'] = $request->boolean('status');
        $attributes['scope'] = $request->boolean('scope') ? Voucher::SPECIFY_SCOPE : Voucher::ALL_SCOPE;
        $attributes['number_times_used_type'] = $request->boolean('number_times_used_type') ? Voucher::TYPE_DAYS : Voucher::TYPE_TIMES;

        return $attributes;
    }

    /**
     * @param int $length
     * @return string
     */
    public function randomUniqueCode(int $length)
    {
        $code = Str::upper(Str::random($length));

        if ($this->model->where('code', $code)->exists()) {
            return $this->randomUniqueCode($length);
        }

        return $code;
    }

    /**
     * @param int $id
     * @param string $status
     * @return Voucher|mixed
     */
    public function updateStatus(int $id, string $status)
    {
        $status = $status == Voucher::AVAILABILITY_STATUS;

        try {
            DB::beginTransaction();

            $this->lockChildForUpdate($id);
            $voucher = $this->model->where('id', $id)->where('status', '!=', $status)->lockForUpdate()->first();
            throw_if(!$voucher, Exception::class, 'Voucher not found.', Response::HTTP_NOT_FOUND);
            $voucher->update([
                'status' => $status
            ]);
            DB::commit();

            return $voucher;
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return false;
        }
    }

    /**
     * @param int $id
     * @return void
     */
    protected function lockChildForUpdate(int $id)
    {
        $ids = $this->savedVoucherService->lockForUpdateByVoucherId($id)->pluck('id');
        $this->historyUseVoucherService->lockForUpdateBySavedIds($ids);
    }
}
