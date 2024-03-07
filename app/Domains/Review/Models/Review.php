<?php

namespace App\Domains\Review\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Review\Models\Traits\Relationship\ReviewRelationship;
use App\Scopes\UserNotHiddenScope;

/**
 * Class Review.
 */
class Review extends Model
{
    use ReviewRelationship;

    protected $table = 'reviews';

    protected $fillable = [
        'star',
        'user_id',
        'job_id',
        'review_id',
        'description'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();
        static::addGlobalScope(new UserNotHiddenScope('userReview'));
    }
}
