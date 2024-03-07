<?php

namespace App\Domains\Message\Models\Traits\Relationship;

use App\Domains\Auth\Models\User;
use App\Domains\MessageFile\Models\MessageFile;
use App\Domains\MessageReaction\Models\MessageReaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class MessageRelationship.
 */
trait MessageRelationship
{
    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function file(): HasOne
    {
        return $this->hasOne(MessageFile::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class, 'message_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function yourReactions(): HasMany
    {
        return $this->reactions();
    }

}
