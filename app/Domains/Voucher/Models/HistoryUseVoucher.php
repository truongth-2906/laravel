<?php

namespace App\Domains\Voucher\Models;

use App\Domains\Voucher\Models\Traits\Relationship\HistoryUsedVoucherRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryUseVoucher extends Model
{
    use SoftDeletes, HistoryUsedVoucherRelation;

    /** @var string */
    protected $table = 'history_use_vouchers';

    /** @var array */
    protected $fillable = [
        'saved_voucher_id',
        'transaction_id',
    ];
}
