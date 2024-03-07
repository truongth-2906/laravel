<?php

namespace App\Domains\Auth\Models;

use App\Domains\Auth\Models\Traits\Relationship\VerifyUserRelationship;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VerifyUser.
 */
class VerifyUser extends Model
{
    use VerifyUserRelationship;

    protected $table = 'verify_users';

    protected $fillable = [
        'user_id',
        'token',
        'token_expired_at'
    ];
}
