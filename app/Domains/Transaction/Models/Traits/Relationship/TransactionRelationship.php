<?php

namespace App\Domains\Transaction\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use App\Domains\Job\Models\Job;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TransactionRelationship.
 */
trait TransactionRelationship
{
    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }
}
