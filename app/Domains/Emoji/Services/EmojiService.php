<?php

namespace App\Domains\Emoji\Services;

use App\Domains\Emoji\Models\Emoji;
use App\Services\BaseService;

/**
 * Class EmojiService.
 */
class EmojiService extends BaseService
{
    /**
     * @param Emoji $message
     */
    public function __construct(Emoji $emoji)
    {
        $this->model = $emoji;
    }

    public function all()
    {
        return $this->model->newQuery()->select('id', 'content')->get();
    }
}
