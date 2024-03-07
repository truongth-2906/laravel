<?php

namespace App\Domains\Voucher\Models\Traits\Method;

use App\Domains\Voucher\Models\Voucher;
use Carbon\Carbon;

trait SavedVoucherMethod
{
    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_DISABLE,
            self::STATUS_SPECIFY,
            self::STATUS_ACTIVATED
        ];
    }

    /**
     * @return mixed
     */
    public function getRemainingTimesAttribute()
    {
        $value = null;

        if ($this->number_times_used_type && $this->number_times_used_value) {
            if ($this->number_times_used_type == Voucher::TYPE_TIMES) {
                $value = $this->number_times_used_value - ($this->historiesUsed->count() ?? 0);
            } else {
                $expiredDateUse = $this->created_at->addDays($this->number_times_used_value);
                $voucherExpiredDate = $this->voucher_expired_date ? Carbon::createFromFormat('Y-m-d', $this->voucher_expired_date) : null;
                if (!$voucherExpiredDate || ($voucherExpiredDate && $voucherExpiredDate->gt($expiredDateUse))) {
                    $value = $expiredDateUse->format('d-m-Y');
                }
                if ($voucherExpiredDate && !$voucherExpiredDate->gt($expiredDateUse)) {
                    $value = $voucherExpiredDate->format('d-m-Y');
                }
            }
        }

        return $value;
    }
}
