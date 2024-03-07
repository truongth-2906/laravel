<?php

namespace App\Domains\Notification\Models;

use App\Domains\Notification\Models\Traits\Method\NotificationMethod;
use App\Domains\Notification\Models\Traits\Relationship\NotificationRelationship;
use App\Domains\Notification\Models\Traits\Scope\NotificationScope;
use App\Scopes\UserNotHiddenScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Notification.
 */
class Notification extends Model
{
    use HasFactory,
        SoftDeletes,
        NotificationRelationship,
        NotificationMethod,
        NotificationScope;

    /** @var string */
    protected $table = 'notifications';

    /** @var array */
    protected $fillable = [
        'sender_id',
        'notifiable_id',
        'notifiable_type',
        'type',
    ];

    public const JOB_APPLY_TYPE = 1;
    public const JOB_DONE_TYPE = 2;
    public const PAYMENT_TYPE = 3;
    public const JOB_NOTIFIABLE_TYPE = 'job';
    public const PAYMENT_NOTIFIABLE_TYPE = 'payment';

    /**
     * @var array
     */
    protected $appends = [
        'title',
        'icon',
        'actions'
    ];

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new UserNotHiddenScope('sender'));
    }
}
