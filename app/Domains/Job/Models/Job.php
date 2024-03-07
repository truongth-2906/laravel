<?php

namespace App\Domains\Job\Models;

use App\Domains\Job\Models\Traits\Method\JobMethod;
use App\Domains\Job\Models\Traits\Scope\JobScope;
use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Job\Models\Traits\Relationship\JobRelationship;

/**
 * Class Job.
 */
class Job extends Model
{
    use JobRelationship,
        JobScope,
        JobMethod,
        HasFactory,
        SoftDeletes;

    protected $table = 'jobs';

    protected $fillable = [
        'name',
        'user_id',
        'timezone_id',
        'country_id',
        'experience_id',
        'company_id',
        'description',
        'wage',
        'category',
        'status',
        'mark_done',
        'due_date',
        'has_sended_mail'
    ];

    public const STATUS_CLOSE = 0;
    public const STATUS_OPEN = 1;
    public const MAX_DESCRIPTION = 1000;
    public const MARK_DONE = 1;
    public const ESCROW_FEE_PERCENTAGE_3 = 0.89 / 100;
    public const ESCROW_FEE_2 = 162.50;
    public const ESCROW_FEE_PERCENTAGE_2 = 0.26 / 100;
    public const ESCROW_FEE_PERCENTAGE_1 = 3.25 / 100;
    public const FIELDS_ALLOWED_SORT = [
        'name' => 'name',
        'status' => 'status',
        'employer_name' => 'employer_name'
    ];

    /**
     * @return JobFactory
     */
    public static function factory()
    {
        return JobFactory::new();
    }

    /** @var array */
    protected $casts = [
        'has_sended_mail' => 'boolean'
    ];
}
