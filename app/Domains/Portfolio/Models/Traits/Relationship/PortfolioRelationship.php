<?php

namespace App\Domains\Portfolio\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use App\Domains\Job\Models\Job;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PortfolioRelationship.
 */
trait PortfolioRelationship
{
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
