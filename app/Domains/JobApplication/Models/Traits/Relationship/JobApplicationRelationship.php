<?php

namespace App\Domains\JobApplication\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use App\Domains\Job\Models\Job;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class JobApplicationRelationship.
 */
trait JobApplicationRelationship
{
    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function job(): HasOne
    {
        return $this->hasOne(Job::class, 'id', 'job_id');
    }
}
