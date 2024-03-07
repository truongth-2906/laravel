<?php

namespace App\Domains\Emoji\Models;

use App\Domains\Emoji\Models\Traits\Relationship\EmojiRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Emoji.
 */
class Emoji extends Model
{
    use HasFactory,
        SoftDeletes,
        EmojiRelation;

    /** @var string */
    protected $table = 'emojis';

    /** @var array */
    protected $fillable = [
        'content',
    ];
}
