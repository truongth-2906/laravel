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

class OverviewMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    protected $message;
    protected $view;
    protected $receiverId;
    protected $messageUnRead;
    public function __construct($message, $view, $receiverId, $messageUnRead)
    {
        $this->message = $message;
        $this->view = $view;
        $this->receiverId = $receiverId;
        $this->messageUnRead = $messageUnRead;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('chat_overview.' . $this->receiverId),
            new PrivateChannel('chat_overview.' . auth()->id()),
        ];
    }


    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $message = $this->message['messageFile'] ?? $this->message['message'];
        return [
            'message' => $message,
            'countUnRead' => $this->messageUnRead,
            'view' => $this->view
        ];
    }
}
