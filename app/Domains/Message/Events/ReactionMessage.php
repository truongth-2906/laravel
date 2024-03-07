<?php

namespace App\Domains\Message\Events;

use App\Domains\Message\Models\Message;
use App\Domains\Notification\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReactionMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var int */
    protected $senderId;

    /** @var Message */
    protected $message;

    /** @var array|\Illuminate\Database\Eloquent\Collection */
    protected $reactions;

    /** @var array|\Illuminate\Database\Eloquent\Collection */
    protected $yourReactions;

    /** @var bool */
    public $afterCommit = true;

    /**
     * Create a new event instance.
     *
     * @param int $senderId
     * @param Message $message
     * @return void
     */
    public function __construct(int $senderId, Message $message)
    {
        $this->senderId = $senderId;
        $this->message = $message;
        $this->reactions = $message->reactions->toArray();
        $this->yourReactions = $message->yourReactions->toArray();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $receiverId = $this->senderId == $this->message->sender_id ? $this->message->receiver_id : $this->message->sender_id;
        return [
            new PrivateChannel('reaction_message.' . $receiverId),
            new PrivateChannel('reaction_message.' . $this->senderId),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'push.reaction_message';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'sender_id' => $this->senderId == $this->message->sender_id ? $this->senderId : $this->message->receiver_id,
                'reactions' => $this->reactions,
                'your_reactions' => $this->yourReactions
            ],
            'sender_key' => echo_token()
        ];
    }
}
