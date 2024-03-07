<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserNotHiddenScope implements Scope
{
    /** @var string */
    protected $relationName = 'user';

    /**
     * @param string|null $relationName
     */
    function __construct($relationName = null)
    {
        if (!is_null($relationName)) {
            $this->relationName = $relationName;
        }
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereHas($this->relationName, function ($e) {
            $e->where('is_hidden', false);
        });
    }
}
