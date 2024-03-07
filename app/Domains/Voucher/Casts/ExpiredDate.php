<?php

namespace App\Domains\Voucher\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ExpiredDate implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Carbon
     */
    public function get($model, $key, $value, $attributes)
    {
        return !is_null($value) && $value != '' ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return Carbon
     */
    public function set($model, $key, $value, $attributes)
    {
        return !is_null($value) && $value != '' ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d') : null;
    }
}
