<?php

namespace App\Domains\Notification\Events;

use App\Domains\Notification\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PushNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Notification */
    protected Notification $notification;

    /** @var int */
    protected $receiverId;

    /** @var bool */
    public $afterCommit = true;

    /**
     * Create a new event instance.
     *
     * @param Notification $notification
     * @param int $receiverId
     * @return void
     */
    public function __construct(Notification $notification, int $receiverId)
    {
        $this->notification = $notification;
        $this->receiverId = $receiverId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notification.' . $this->receiverId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'push.notification';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'notification' => [
                'id' => $this->notification->id,
                'title' => $this->notification->title,
                'icon' => $this->notification->icon,
                'actions' => [
                    'title' => $this->notification->actions->get('title', ''),
                    'route' => $this->notification->actions->get('route', '#'),
                ],
                'type' => $this->notification->type,
                'content' => __('Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid pariatur, ipsum similique veniam.')
            ]
        ];
    }
}
