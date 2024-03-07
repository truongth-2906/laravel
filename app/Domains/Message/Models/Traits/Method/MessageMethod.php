<?php

namespace App\Domains\Message\Models\Traits\Method;

/**
 * Class MessageMethod.
 */
trait MessageMethod
{
    /**
     * @return bool
     */
    public function isUserSender(): bool
    {
        return $this->sender_id === auth()->id();
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }
}
