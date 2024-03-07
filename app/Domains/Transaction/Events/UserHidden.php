<?php

namespace App\Domains\Transaction\Events;

use App\Domains\Auth\Models\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserHidden.
 */
class UserHidden
{
    use SerializesModels;

    /** @var User */
    public $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
