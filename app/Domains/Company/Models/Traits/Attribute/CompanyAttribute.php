<?php

namespace App\Domains\Company\Models\Traits\Attribute;

/**
 * Trait CompanyAttribute.
 */
trait CompanyAttribute
{
    /**
     * @return mixed
     */
    public function getAvatarAttribute()
    {
        return $this->getAvatar();
    }
}
