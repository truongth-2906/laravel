<?php

namespace App\Domains\MessageFile\Http\Controllers;

use App\Domains\MessageFile\Services\MessageFileService;
use App\Http\Controllers\Controller;

class FileMessageController extends Controller
{
    /** @var MessageFileService */
    protected $messageFileService;

    /**
     * @param MessageFileService $messageFileService
     */
    public function __construct(MessageFileService $messageFileService)
    {
        $this->messageFileService = $messageFileService;
    }

    /**
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($filename)
    {
        return $this->messageFileService->downloadFile($filename);
    }
}
