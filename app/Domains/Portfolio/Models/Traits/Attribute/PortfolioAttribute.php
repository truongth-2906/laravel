<?php

namespace App\Domains\Portfolio\Models\Traits\Attribute;

/**
 * Trait PortfolioAttribute.
 */
trait PortfolioAttribute
{
    /**
     * @return string
     */
    public function getLinkAttribute(): string
    {
        return $this->getLink();
    }
}
