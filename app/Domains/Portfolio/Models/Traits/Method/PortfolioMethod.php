<?php

namespace App\Domains\Portfolio\Models\Traits\Method;

use Illuminate\Support\Facades\Storage;

/**
 * Trait PortfolioMethod.
 */
trait PortfolioMethod
{
    /**
     * @return string
     */
    public function getLink(): string
    {
        return Storage::disk('azure')->url('/public/portfolios/' . $this->file);
    }
}
