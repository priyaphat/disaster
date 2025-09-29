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
        Log::info('TEAM IDS');
        $this->teamIds = $teamIds;
    }

    public function broadcastOn()
    {
        Log::info('EMC REQUEST NOTIFICATION');
        // if ($this->teamIds) {
            return [
                collect($this->teamIds)
                    ->map(fn($id) => new PrivateChannel("team.$id"))
                    ->all(),
                new PrivateChannel("admin"),
            ];
        return new PrivateChannel("admin");
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
