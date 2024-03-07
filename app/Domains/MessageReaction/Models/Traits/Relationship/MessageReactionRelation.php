<?php

namespace App\Domains\MessageReaction\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use App\Domains\Emoji\Models\Emoji;
use App\Domains\Message\Models\Message;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MessageReactionRelation.
 */
trait MessageReactionRelation
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emoji(): BelongsTo
    {
        return $this->belongsTo(Emoji::class, 'emoji_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
