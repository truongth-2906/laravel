<?php

namespace App\Domains\Company\Models\Traits\Method;

use Illuminate\Support\Facades\Storage;

/**
 * Trait CompanyMethod.
 */
trait CompanyMethod
{
    /**
     * @param $size
     * @return string|null
     */
    public function getAvatar($size = null): ?string
    {
        return Storage::disk('azure')->url('/public/companies/' . $this->logo);
    }
}
