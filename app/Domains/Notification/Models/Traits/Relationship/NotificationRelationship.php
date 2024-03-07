<?php

namespace App\Domains\Notification\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use App\Domains\NotificationUser\Models\NotificationUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Notification;

/**
 * Class NotificationRelationship.
 */
trait NotificationRelationship
{
    /**
     * @return MorphTo
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo(null, 'notifiable_type', 'notifiable_id');
    }

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function receivers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user', 'notification_id', 'user_id')
        ->whereNull('notification_user.deleted_at')
        ->withPivot('is_read')
        ->withTimestamps()
        ->as('receiver_pivot');
    }

    /**
     * @return HasOne
     */
    public function receiverPivot(): HasOne
    {
        return $this->hasOne(NotificationUser::class, 'notification_id', 'id');
    }
}
