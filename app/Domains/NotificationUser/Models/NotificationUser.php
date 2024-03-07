<?php

namespace App\Domains\NotificationUser\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationUser extends Model
{
    use SoftDeletes;

    /** @var string */
    protected $table = 'notification_user';

    /** @var array */
    protected $fillable = [
        'user_id',
        'notification_id',
        'is_read',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];
}
