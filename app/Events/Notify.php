<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class Notify implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $userId;
    public function __construct($userId)
    {
        //
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('Notify.User.' . $this->userId),
        ];
    }

     public function broadcastWith(): array
    {
        return array(
            'count' => 1,
        );
    }

    public function broadcastAs(): string
    {
        return 'user.notify';
    }

}
