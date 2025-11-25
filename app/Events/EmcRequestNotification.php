<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmcRequestNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $teamIds;

    public function __construct($teamIds = null)
    {
        $this->teamIds = $teamIds;
    }

    public function broadcastOn()
    {
        $channels[] = new PrivateChannel('admin');

        if ($this->teamIds) {
            foreach ($this->teamIds as $teamId) {
                $channels[] = new PrivateChannel('team.'. $teamId);
            }
        }

        return $channels;
    }

    public function broadcastAs()
    {
        return 'user.emc-notification';
    }

    public function broadcastWith()
    {
        return [
            'action' => 'refresh api'
        ];
    }
}
