<?php

namespace App\Domains\MessageFile\Models;

use App\Domains\Message\Models\Message;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'size'
    ];

    /** @var array */
    protected $appends = [
        'download_url',
    ];

    /**
     * @return string
     */
    public function getDownloadUrlAttribute()
    {
        return $this->file ? route('frontend.download.file_message', $this->file) : '';
    }

    /**
     * @return BelongsTo
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id', 'id');
    }
}
