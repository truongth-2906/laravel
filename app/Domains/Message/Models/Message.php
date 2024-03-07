<?php

namespace App\Domains\Message\Models;

use App\Domains\Message\Models\Traits\Method\MessageMethod;
use App\Domains\Message\Models\Traits\Relationship\MessageRelationship;
use App\Domains\Message\Models\Traits\Scope\MessageScope;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Message.
 */
class Message extends Model
{
    use HasFactory,
        SoftDeletes,
        MessageScope,
        MessageRelationship,
        MessageMethod;

    /** @var string */
    protected $table = 'messages';

    /** @var array */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'reactions',
        'is_read',
        'message',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_read' => 'boolean',
        'last_texting_time' => 'datetime'
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
