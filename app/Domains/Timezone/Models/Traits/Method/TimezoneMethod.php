<?php

namespace App\Domains\Timezone\Models\Traits\Method;

trait TimezoneMethod
{
    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->city . ' - ' . $this->diff_from_gtm;
    }
}
