<?php

namespace App\Domains\Portfolio\Models;

use App\Domains\Portfolio\Models\Traits\Attribute\PortfolioAttribute;
use App\Domains\Portfolio\Models\Traits\Method\PortfolioMethod;
use App\Domains\Portfolio\Models\Traits\Relationship\PortfolioRelationship;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Portfolio.
 */
class Portfolio extends Model
{
    use PortfolioRelationship,
        PortfolioMethod,
        PortfolioAttribute;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'job_id',
        'name',
        'file',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'link'
    ];
}
