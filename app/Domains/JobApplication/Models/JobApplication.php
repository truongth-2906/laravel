<?php

namespace App\Domains\JobApplication\Models;

use App\Domains\JobApplication\Models\Traits\Relationship\JobApplicationRelationship;
use App\Scopes\UserNotHiddenScope;
use Illuminate\Database\Eloquent\Model;
/**
 * Class Job application.
 */
class JobApplication extends Model
{
    use JobApplicationRelationship;

    protected $table = 'job_applications';

    protected $fillable = [
        'user_id',
        'job_id',
        'status'
    ];

    public const STATUS_REJECT = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVE = 2;
    public const STATUS_DONE = 3;
    public const STATUS_ESCROW_HANDLING = 4;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();
        static::addGlobalScope(new UserNotHiddenScope);
    }
}
