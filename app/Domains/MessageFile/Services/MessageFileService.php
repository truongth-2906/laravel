<?php

namespace App\Domains\MessageFile\Services;

use App\Domains\MessageFile\Models\MessageFile;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Class MessageService.
 */
class MessageFileService extends BaseService
{
    public const PATH_TO_MESSAGE = '/public/messages/';
    public const DISK = 'azure';

    /**
     * @param MessageFile $messageFile
     */
    public function __construct(MessageFile $messageFile)
    {
        $this->model = $messageFile;
    }

    public function createFileMessage($file, $message)
    {
        if (isset($file)) {
            $fileSave = now()->timestamp . $file->getSize(). '.' . $file->extension();
            $file->storeAs($this::PATH_TO_MESSAGE, $fileSave, $this::DISK);
            $fileName = $file->getClientOriginalName();
            return $this->model->create([
                'message_id' => $message->id,
                'name' => $fileName,
                'file' => $fileSave,
                'size' => $file->getSize()
            ]);
        }
        return false;
    }

    /**
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadFile(string $filename)
    {
        $fileMessage = $this->model->has('message')->where('file', $filename)->with('message:id,sender_id,receiver_id')->first();
        /** @var \App\Domains\Auth\Models\User */
        $user = auth()->user();

        abort_if(
            !$fileMessage ||
                (!$user->isAdmin() &&
                    $user->id != $fileMessage->message->sender_id && $user->id != $fileMessage->message->receiver_id) ||
                !Storage::disk($this::DISK)->exists($this::PATH_TO_MESSAGE . $filename),
            Response::HTTP_NOT_FOUND
        );

        return Storage::disk($this::DISK)->download($this::PATH_TO_MESSAGE . $filename, $fileMessage->name);
    }
}
