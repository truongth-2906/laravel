<?php

namespace App\Domains\MessageReaction\Models;

use App\Domains\MessageReaction\Models\Traits\Relationship\MessageReactionRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MessageReaction.
 */
class MessageReaction extends Model
{
    use HasFactory, MessageReactionRelation;

    /** @var string */
    protected $table = 'message_reaction';

    /** @var array */
    protected $fillable = [
        'user_id',
        'message_id',
        'emoji_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
