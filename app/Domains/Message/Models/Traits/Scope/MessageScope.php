<?php

namespace App\Domains\Message\Models\Traits\Scope;

/**
 * Class MessageScope.
 */
trait MessageScope
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeUserNotHidden($query)
    {
        /** @var \App\Domains\Auth\Models\User */
        $user = auth()->user();

        $query->when(!$user->isAdmin(), function ($q) {
            $q->whereHas('sender', function ($e) {
                $e->where('is_hidden', false);
            })->whereHas('receiver', function ($e) {
                $e->where('is_hidden', false);
            });
        });
    }
}
