<?php

namespace App\Domains\Notification\Models\Traits\Scope;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class NotificationScope.
 */
trait NotificationScope
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasUnread($query)
    {
        return $query->where('is_read', false);
    }
}
