<?php

namespace App\Domains\Voucher\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DiscountConversion implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return int|float
     */
    public function get($model, $key, $value, $attributes)
    {
        $valueConverted = intval($value);

        if ($valueConverted == $value) {
            return $valueConverted;
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return int|float
     */
    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
