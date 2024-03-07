<?php

namespace App\Domains\Saved\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class SavedRelationship.
 */
trait SavedRelationship
{
    /**
     * @return MorphTo
     */
    public function savedable(): MorphTo
    {
        return $this->morphTo(null, 'saved_type', 'saved_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
