<?php

namespace App\Domains\Voucher\Models;

use App\Domains\Voucher\Models\Traits\Method\SavedVoucherMethod;
use App\Domains\Voucher\Models\Traits\Relationship\SavedVoucherRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavedVoucher extends Model
{
    use SoftDeletes, SavedVoucherMethod, SavedVoucherRelationship;

    /** @var string */
    protected $table = 'saved_vouchers';

    /** @var array */
    protected $fillable = [
        'user_id',
        'voucher_id',
        'status',
    ];

    //Voucher used statuses
    public const STATUS_DISABLE = 0;
    public const STATUS_SPECIFY = 1;
    public const STATUS_ACTIVATED = 2;
}
