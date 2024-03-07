<?php

namespace App\Domains\Voucher\Models\Traits\Method;

trait VoucherMethod
{
    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_PERCENTAGE,
            self::TYPE_NUMERIC
        ];
    }

    /**
     * @return array
     */
    public static function getTypesName()
    {
        return [
            self::TYPE_PERCENTAGE => 'Percentage',
            self::TYPE_NUMERIC => 'Numeric'
        ];
    }

    /**
     * @return string
     */
    public function getTypeNameAttribute()
    {
        $types = self::getTypesName();
        return $types[$this->type];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getFieldAllowSort(string $key)
    {
        if (array_key_exists($key, $this::FIELDS_ALLOWED_SORT)) {
            return $this::FIELDS_ALLOWED_SORT[$key];
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getNumberTimesUsedTypes()
    {
        return [
            self::TYPE_TIMES,
            self::TYPE_DAYS
        ];
    }

    /**
     * @return array
     */
    static public function getStatuses()
    {
        return [
            self::DISABLED_STATUS,
            self::AVAILABILITY_STATUS
        ];
    }
}
