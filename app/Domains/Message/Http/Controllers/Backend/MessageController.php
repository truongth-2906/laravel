<?php

namespace App\Domains\Message\Http\Controllers\Backend;

use App\Domains\Emoji\Services\EmojiService;
use App\Domains\Message\Services\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessageController extends Controller
{
    /** @var MessageService */
    protected $messageService;

    /** @var EmojiService */
    protected $emojiService;

    /**
     * @param MessageService $messageService
     * @param EmojiService $emojiService
     */
    public function __construct(MessageService $messageService, EmojiService $emojiService)
    {
        $this->messageService = $messageService;
        $this->emojiService = $emojiService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function index(Request $request)
    {
        $messageGroups = $this->messageService->listAllGroupChat($request);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Message groups table',
                'html' => view('backend.message.table', compact('messageGroups'))->render(),
                'total' => $messageGroups->total()
            ], Response::HTTP_OK);
        }

        return view('backend.message.index', compact('messageGroups'));
    }

    /**
     * @param int $senderId
     * @param int $receiverId
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show($senderId, $receiverId, Request $request)
    {
        if (!request()->ajax()) {
            request()->merge(['page' => 1]);
        }
        $messages = $this->messageService->getGroupMessage($senderId, $receiverId);

        if (request()->ajax()) {
            return response()->json([
                'message' => 'Messages',
                'data' => $messages->reverse(),
                'has_next_page' => $messages->nextPageUrl() ? true : false,
                'current_page' => $messages->currentPage()
            ], Response::HTTP_OK);
        }
        $emojis = $this->emojiService->get();

        return view('backend.message.show', compact('emojis', 'messages'));
    }
}
