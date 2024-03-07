<?php

namespace App\Domains\Message\Services;

use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\UserService;
use App\Domains\Message\Models\Message;
use App\Domains\MessageFile\Services\MessageFileService;
use App\Services\BaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * Class MessageService.
 */
class MessageService extends BaseService
{
    /**
     * @param Message $message
     */

    /**
     * @var MessageFileService
     */
    protected MessageFileService $messageFileService;

    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @param Message $message
     * @param MessageFileService $messageFileService
     * @param UserService $userService
     */
    public function __construct(
        Message            $message,
        MessageFileService $messageFileService,
        UserService $userService
    ) {
        $this->model = $message;
        $this->messageFileService = $messageFileService;
        $this->userService = $userService;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getListLastMessageChat($request)
    {
        $usersOnlineId = $this->userService->getUsersOnlineId([User::TYPE_EMPLOYER, User::TYPE_FREELANCER]);

        return $this->model->select([
            MESSAGES_TABLE_NAME . '.*',
        ])
            ->userNotHidden()
            ->with('sender', function ($query) use ($usersOnlineId) {
                $query->select('id', 'type', 'name', 'email', 'avatar', 'company_id')->selectOnlineStatus($usersOnlineId)->with('company:id,logo')->get();
            })->with('receiver', function ($query) use ($usersOnlineId) {
                $query->select('id', 'type', 'name', 'email', 'avatar', 'company_id')->selectOnlineStatus($usersOnlineId)->with('company:id,logo')->get();
            })
            ->whereIn('messages.id', $this->listMessageIdByUser())
            ->when($request->nameUser, function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->orWhereHas('sender', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . escapeLike($request->nameUser) . '%');
                    })->orWhereHas('receiver', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . escapeLike($request->nameUser) . '%');
                    });
                });
            })
            ->orderBy('messages.created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * @return mixed
     */
    public function listMessageIdByUser()
    {
        $userId = auth()->id();
        return $this->model->select(
            DB::raw('max(id) as message_id')
        )->distinct()
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->userNotHidden()
            ->groupBy(DB::raw('least(sender_id, receiver_id), greatest(sender_id, receiver_id)'))
            ->get()->pluck('message_id')->toArray();
    }

    /**
     * @param $param
     * @param $id
     * @return array|false
     * @throws \Throwable
     */
    public function store($param, $id)
    {
        try {
            DB::beginTransaction();
            $messageFile = null;
            $messageText = null;
            if ($param->message) {
                $messageText = $this->model->create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $id,
                    'message' => $param->message,
                    'type' => TYPE_MESSAGE_TEXT,
                    'is_read' => UN_READ_MESSAGE
                ]);
            }
            if ($param->file) {
                $messageFile = $this->model->create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $id,
                    'type' => TYPE_MESSAGE_FILE,
                    'is_read' => UN_READ_MESSAGE
                ]);
                $file = $this->messageFileService->createFileMessage($param->file, $messageFile);
                if (!$file) {
                    DB::rollBack();
                    return false;
                }
            }

            DB::commit();

            return [
                'message' => $messageText,
                'messageFile' => $messageFile ? $messageFile->load('file') : $messageFile
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * @param $id
     * @param false $offset
     * @return mixed
     */
    public function getMessagesChat($id, $offset = false)
    {
        $userId = auth()->id();
        $usersOnlineId = $this->userService->getUsersOnlineId([User::TYPE_EMPLOYER, User::TYPE_FREELANCER]);
        $query = $this->model
            ->where([
                ['sender_id', $userId], ['receiver_id', $id]
            ])
            ->orWhere([
                ['sender_id', $id], ['receiver_id', $userId]
            ])
            ->userNotHidden()
            ->with('sender', function ($e) use ($userId, $usersOnlineId) {
                $e->select('id')->selectOnlineStatus($usersOnlineId)->where('id', '!=', $userId);
            })
            ->with('reactions', function ($e) use ($userId) {
                $this->queryToMessageReaction($e, $userId);
            })
            ->orderBy('created_at', 'desc');
        if ($offset) {
            return $query->offset($offset)->limit(LOAD_MORE_QUANTITY)->get()->reverse();
        }
        return $query->paginate(LOAD_MORE_QUANTITY)->reverse();
    }

    /**
     * @return mixed
     */
    public function getUserContactLatest()
    {
        $userId = auth()->id();
        return $this->model
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->userNotHidden()
            ->orderBy('created_at', 'desc')->first();
    }

    /**
     * @return int
     */
    public function countUnreadMessages()
    {
        $this->newQuery();

        return $this->query
            ->userNotHidden()
            ->where([
                'receiver_id' => auth()->id(),
                'is_read' => false
            ])
            ->count();
    }

    /**
     * @param int $id
     * @param bool $isAddReact
     * @param mixed ...$reacts
     * @return \App\Domains\Message\Models\Message
     */
    public function handleReaction(int $id, bool $isAddReact, ...$reacts)
    {
        $userId = auth()->id();
        $message = $this->findWithUserId($id, $userId, ['reactions' => function ($e) use ($userId) {
            $e->where('user_id', $userId);
        }]);

        if ($isAddReact) {
            $this->addReaction($message, $userId, $reacts);
        } else {
            $this->removeReaction($message, $userId, $reacts);
        }


        $receiverId = $userId == $message->sender_id ? $message->receiver_id : $message->sender_id;
        $message->load([
            'reactions' => fn ($e) => $this->queryToMessageReaction($e, $receiverId),
            'yourReactions' => fn ($e) => $this->queryToMessageReaction($e, $userId)
        ]);

        return $message;
    }

    /**
     * @param \App\Domains\Message\Models\Message $message
     * @param int $userId
     * @param array $reacts
     * @return bool
     */
    public function addReaction(Message $message, int $userId, array $reacts)
    {
        $result = false;

        if (count($reacts)) {
            $reactsInsert = [];
            foreach ($reacts as $react) {
                if ($message->reactions->isEmpty() || !$message->reactions->where('emoji_id', $react)->count()) {
                    $result = true;
                    $reactsInsert[] = [
                        'message_id' => $message->id,
                        'user_id' => $userId,
                        'emoji_id' => $react,
                    ];
                }
            }
            if (count($reactsInsert)) {
                $message->reactions()->insert($reactsInsert);
            }
        }

        throw_if(!$result, Exception::class, 'Reaction failed.', Response::HTTP_INTERNAL_SERVER_ERROR);

        return true;
    }

    /**
     * @param \App\Domains\Message\Models\Message $message
     * @param int $userId
     * @param array $reacts
     * @return bool
     */
    public function removeReaction(Message $message, int $userId, array $reacts)
    {
        $result = false;

        if (count($reacts) && $message->reactions->isNotEmpty()) {
            $reactsDelete = [];
            foreach ($reacts as $react) {
                if ($message->reactions->where('emoji_id', $react)->count()) {
                    $result = true;
                    $reactsDelete[] = $react;
                }
            }
            if (count($reactsDelete)) {
                $message->reactions()->where('user_id', $userId)->whereIn('emoji_id', $reactsDelete)->delete();
            }
        }

        throw_if(!$result, Exception::class, 'Reaction failed.', Response::HTTP_INTERNAL_SERVER_ERROR);

        return true;
    }

    /**
     * @param int $id
     * @param int $userId
     * @param array $with
     * @return \App\Domains\Message\Models\Message
     */
    public function findWithUserId(int $id, int $userId, array $with = [])
    {
        $message = $this->model->where('id', $id)
            ->userNotHidden()
            ->where(function ($e) use ($userId) {
                $e->orWhere('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->has('sender')
            ->has('receiver')
            ->when(count($with), function ($e) use ($with) {
                $e->with($with);
            })
            ->first();

        throw_if(!$message, Exception::class, 'Message not found.', Response::HTTP_NOT_FOUND);

        return $message;
    }

    /**
     * @param mixed $query
     * @param int $userId
     * @return mixed
     */
    public function queryToMessageReaction($query, int $userId, bool $isGetTitle = false)
    {
        return $query->select(
            'message_reaction.id as message_reaction_id',
            'message_reaction.message_id as message_id',
            'message_reaction.user_id as user_id',
            'message_reaction.emoji_id as emoji_id',
            'emojis.content as emoji_content',
            DB::raw("CAST(SUM(IF(message_reaction.user_id = $userId, 1, 0)) AS UNSIGNED) as is_reacted"),
            DB::raw("COUNT(*) as count")
        )
            ->when($isGetTitle, function ($e) {
                $e->addSelect(DB::raw("(SELECT GROUP_CONCAT(DISTINCT u.name SEPARATOR ', ') FROM users u WHERE u.id IN (SELECT DISTINCT m.user_id FROM message_reaction m WHERE m.message_id = message_reaction.message_id AND m.emoji_id = message_reaction.emoji_id)) as title"));
            })
            ->join('users', 'users.id', '=', 'message_reaction.user_id')
            ->join('emojis', 'emojis.id', '=', 'message_reaction.emoji_id')
            ->groupByRaw('message_id, emoji_id')
            ->orderBy('message_reaction_id', 'asc')
            ->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getFirstMessageByUserId($id)
    {
        return $this->model->select('id')
            ->where([
                ['sender_id', auth()->id()], ['receiver_id', $id]
            ])
            ->orWhere([
                ['sender_id', $id], ['receiver_id', auth()->id()]
            ])
            ->userNotHidden()
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * @param $userId
     * @param $id
     * @return mixed
     */
    public function getOffsetRecord($userId, $id)
    {
        return $this->model
            ->userNotHidden()
            ->where(function ($query) use ($userId) {
                $query->where([
                    ['sender_id', auth()->id()], ['receiver_id', $userId]
                ])->orWhere([
                    ['sender_id', $userId], ['receiver_id', auth()->id()]
                ]);
            })->where('id', '>=', $id)->count();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function markIsRead($id)
    {
        return $this->model
            ->where([
                'receiver_id' => auth()->id(), 'sender_id' => $id, 'is_read' => false
            ])->update(['is_read' => true]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function countUnreadMessagesById($id)
    {
        return $this->model
            ->userNotHidden()
            ->where([
                'receiver_id' => $id,
                'is_read' => false
            ])
            ->count();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function listAllGroupChat(Request $request)
    {
        return $this->model->select('*')
            ->addSelect(DB::raw('MAX(created_at) as last_texting_time'))
            ->when(!is_null($request->search), function ($e) use ($request) {
                $search = escapeLike($request->search);
                $e->orWhereHas('sender', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                    ->orWhereHas('receiver', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->when(strtotime($request->search) !== false, function ($e) use ($request) {
                        $search = date('Y-m-d', strtotime($request->search));
                        $e->orWhereDate('created_at', '=', $search);
                    });
            })
            ->with('sender', 'receiver')
            ->groupBy(DB::raw('least(sender_id, receiver_id), greatest(sender_id, receiver_id)'))
            ->orderBy('last_texting_time', strtoupper($request->order_by ?? '') == TYPE_SORT_ASC ? TYPE_SORT_ASC : TYPE_SORT_DESC)
            ->paginate(config('paging.quantity'));
    }

    /**
     * @param int $senderId
     * @param int $receiverId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getGroupMessage(int $senderId, int $receiverId)
    {
        $query = $this->model
            ->where([
                ['sender_id', $senderId], ['receiver_id', $receiverId]
            ])
            ->orWhere([
                ['sender_id', $receiverId], ['receiver_id', $senderId]
            ])
            ->with('sender', 'receiver', 'file', 'sender.company', 'receiver.company')
            ->with('reactions', function ($e) {
                $this->queryToMessageReaction($e, 0, true);
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate(LOAD_MORE_QUANTITY);
    }
}
