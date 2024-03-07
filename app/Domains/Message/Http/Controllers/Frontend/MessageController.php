<?php

namespace App\Domains\Message\Http\Controllers\Frontend;

use App\Domains\Message\Services\MessageService;
use App\Domains\Auth\Services\UserService;
use App\Domains\Message\Events\ReactionMessage;
use App\Events\ChatMessage;
use App\Events\OverviewMessage;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Domains\Message\Http\Requests\StoreMessageRequest;
use App\Domains\Emoji\Services\EmojiService;
use Illuminate\Http\Response;

/**
 * class Message Controller
 */
class MessageController
{
    /**
     * @var MessageService $messageService
     */
    protected MessageService $messageService;

    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @var EmojiService
     */
    protected EmojiService $emojiService;

    /**
     * @param MessageService $messageService
     * @param UserService $userService
     * @param EmojiService $emojiService
     */
    public function __construct(
        MessageService $messageService,
        UserService    $userService,
        EmojiService   $emojiService
    )
    {
        $this->messageService = $messageService;
        $this->userService = $userService;
        $this->emojiService = $emojiService;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|JsonResponse|RedirectResponse|void
     */
    public function index(Request $request)
    {
        try {
            $lastMessages = $this->messageService->getListLastMessageChat($request);
            $usersId = $lastMessages->pluck('sender_id')->merge($lastMessages->pluck('receiver_id'))->unique()->toArray();

            if ($request->ajax()) {
                $view = view('frontend.message.list_user_chat', compact('lastMessages'))->render();
                return response()->json([
                    'html' => $view
                ]);
            }
            $userLatest = $this->messageService->getUserContactLatest();
            $emojis = $this->emojiService->get();
            if ($userLatest) {
                return redirect()->route(USER_CHAT_MESSAGE_ROUTE, $userLatest->isUserSender() ? $userLatest->receiver_id : $userLatest->sender_id);
            }
            return view('frontend.message.index', compact('lastMessages', 'emojis'));
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'html' => '',
                    'error' => true,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|Factory|View|JsonResponse
     */
    public function chat(Request $request, $id)
    {
        $oldest = $this->messageService->getFirstMessageByUserId($id);
        $userInfo = $this->userService->getById($id, true);
        $emojis = $this->emojiService->get();
        if ($request->ajax()) {
            $offset = $this->messageService->getOffsetRecord($id, $request->messageId);
            $messages = $this->messageService->getMessagesChat($id, $offset);
            $nearMessage = $this->messageService->getById($request->messageId);
            $time = formatDateChat(optional($nearMessage)->created_at);
            $view = view('frontend.message.message', compact('messages', 'oldest', 'userInfo'))->render();

            return response()->json(['html' => $view, 'time' => $time]);
        }
        $lastMessages = $this->messageService->getListLastMessageChat($request);
        $messages = $this->messageService->getMessagesChat($id);
        $this->messageService->markIsRead($id);
        $usersId = $lastMessages->pluck('sender_id')->merge($lastMessages->pluck('receiver_id'))->unique()->toArray();

        return view('frontend.message.index', compact('lastMessages', 'messages', 'userInfo', 'oldest', 'emojis'));
    }

    /**
     * @param StoreMessageRequest $request
     * @param $id
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(StoreMessageRequest $request, $id)
    {
        $user = $this->userService->getById(auth()->id());
        $isOnline = $this->userService->isOnline($user->id);
        $message = $this->messageService->store($request, $id);
        $messageUnRead = $this->messageService->countUnreadMessagesById($id);
        $view = view('frontend.message.preview', compact('message', 'isOnline'))->render();
        broadcast(new ChatMessage($message['message'], $user, $message['messageFile'], $id))->toOthers();
        broadcast(new OverviewMessage($message, $view, $id, $messageUnRead))->toOthers();

        return response()->json(['data' => $message]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|RedirectResponse
     */
    public function reaction(Request $request, $id)
    {
        try {
            $message = $this->messageService->handleReaction($id, $request->method() == 'POST', $request->get('reaction'));
            broadcast(new ReactionMessage(auth()->id(), $message))->toOthers();

            if ($request->ajax()) {
                return response()->json([
                    'message' => __('Reaction success.')
                ], Response::HTTP_OK);
            }

            return redirect()->back()->with('message', __('Reaction success.'));
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(
                    [
                        'message' => __('Reaction failed.'),
                        'error' => true,
                    ],
                    $e->getCode() == Response::HTTP_NOT_FOUND ? Response::HTTP_NOT_FOUND : Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return redirect()->back()->with('error', __('Reaction failed.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListUser(Request $request)
    {
        $latest = $this->userService->getUserLatest($request);
        $users = $this->userService->getListUserChat($request);
        $view = view('frontend.message.render_list_user', compact('users', 'latest'))->render();

        return response()->json(['html' => $view]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function markIsRead(Request $request)
    {
        $this->messageService->markIsRead($request->id);
        $messageUnRead = $this->messageService->countUnreadMessages();

        return response()->json(['count' => $messageUnRead]);
    }
}
