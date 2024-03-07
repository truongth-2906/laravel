<?php

namespace App\Domains\Voucher\Models;

use App\Domains\Voucher\Casts\DiscountConversion;
use App\Domains\Voucher\Casts\ExpiredDate;
use App\Domains\Voucher\Models\Traits\Method\VoucherMethod;
use App\Domains\Voucher\Models\Traits\Relationship\VoucherRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes, VoucherMethod, VoucherRelationship;

    /** @var string */
    protected $table = 'vouchers';

    /** @var array */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'code',
        'type',
        'discount',
        'max_discount',
        'count',
        'status',
        'expired_date',
        'scope',
        'number_times_used_type',
        'number_times_used_value'
    ];

    /** @var array */
    protected $casts = [
        'status' => 'boolean',
        'expired_date' => ExpiredDate::class,
        'discount' => DiscountConversion::class
    ];

    //Voucher types
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_NUMERIC = 'numeric';

    //Voucher number times used types
    public const TYPE_TIMES = 'times';
    public const TYPE_DAYS = 'days';

    //Voucher scopes
    public const ALL_SCOPE = 1;
    public const SPECIFY_SCOPE = 2;

    //Length code
    public const LENGTH_CODE = 16;

    public const FIELDS_ALLOWED_SORT = [
        'name',
        'discount',
        'type',
        'count',
        'count_used',
        'expired_date'
    ];

    public const MAX_DESCRIPTION = 1000;

    //Voucher statuses
    public const DISABLED_STATUS = 'disabled';
    public const AVAILABILITY_STATUS = 'availability';
}
