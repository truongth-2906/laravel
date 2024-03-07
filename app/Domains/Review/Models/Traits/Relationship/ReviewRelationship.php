<?php

namespace App\Domains\Review\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ReviewRelationship.
 */
trait ReviewRelationship
{
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function userReview(): BelongsTo
    {
        return $this->belongsTo(User::class, 'review_id');
    }
}
