<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProductNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;


    public $action;

    public function __construct($action = 'refresh')
    {
        $this->action = $action;
    }

    public function broadcastOn()
    {
        return new Channel('Product');
    }

    public function broadcastAs()
    {
        return 'page.refresh';
    }

    public function broadcastWith()
    {
        return [
            'action' => $this->action,
        ];
    }
}
