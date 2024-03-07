<?php

namespace App\Domains\Emoji\Models\Traits\Relationship;

use App\Domains\MessageReaction\Models\MessageReaction;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class EmojiRelation.
 */
trait EmojiRelation
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class, 'emoji_id', 'id');
    }
}
