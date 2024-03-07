<?php

namespace App\Domains\Saved\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Saved\Models\Traits\Relationship\SavedRelationship;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Saved.
 */
class Saved extends Model
{
    use SavedRelationship;

    protected $table = 'saveds';

    protected $fillable = [
        'user_id',
        'saved_id',
        'saved_type',
    ];

    public const TYPE_FREELANCER = 'freelancer';
    public const TYPE_JOB = 'job';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();
        static::addGlobalScope('userHidden', function (Builder $builder) {
            //Only take rows with type of job or freelancer that are not hidden.
            $builder->whereRaw('IF(saveds.saved_type = ? OR (saveds.saved_type = ? AND EXISTS(SELECT u.is_hidden FROM users u WHERE u.id = saveds.saved_id AND u.is_hidden = ?)), 1, 0) = 1', [
                Saved::TYPE_JOB, Saved::TYPE_FREELANCER, 0
            ]);
        });
    }
}
