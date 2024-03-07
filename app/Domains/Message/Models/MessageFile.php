<?php

namespace App\Domains\Message\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MessageFile.
 */
class MessageFile extends Model
{
    use HasFactory,
        SoftDeletes;

    /** @var string */
    protected $table = 'message_files';

    /** @var array */
    protected $fillable = [
        'message_id',
        'name',
        'file',
    ];
}
