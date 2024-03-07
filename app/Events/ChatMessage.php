<?php

namespace App\Events;

use App\Domains\Auth\Models\User;
use App\Domains\Message\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    protected $message;
    protected User $user;
    protected $messageFile;
    protected $receiverId;
    public function __construct($message, $user, $messageFile, $receiverId)
    {
        $this->message = $message;
        $this->user = $user;
        $this->messageFile = $messageFile;
        $this->receiverId = $receiverId;
    }

    /**
     * @return PrivateChannel[]
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('chatroom.' . $this->user->id . '.' . $this->receiverId),
            new PrivateChannel('chatroom.' . $this->user->id . '.' . $this->user->id),
        ];
    }


    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $messageText = [];
        $messageFile = [];
        if ($this->message) {
            $messageText = [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'message' => $this->message->message,
                'reactions' => $this->message->reactions,
                'created_at' => $this->message->created_at,
                'type' => $this->message->type,
                'file' => $this->message->file,
                'user' => $this->message->sender,
            ];
        }
        if ($this->messageFile) {
            $messageFile = [
                'message' => $this->messageFile,
                'file' => optional($this->messageFile)->file,
                'user' => optional($this->messageFile)->sender
            ];
        }
        return [
            'message' => [
                'text' => $messageText,
                'file' => $messageFile
            ]
        ];
    }
}
