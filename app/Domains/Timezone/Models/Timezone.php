<?php

namespace App\Domains\Timezone\Models;

use App\Domains\Timezone\Models\Traits\Method\TimezoneMethod;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Timezone.
 */
class Timezone extends Model
{
    use TimezoneMethod;

    protected $table = 'timezones';

    /**
     * @var string[]
     */
    protected $fillable = [
        'city',
        'offset',
        'diff_from_gtm',
    ];
}
