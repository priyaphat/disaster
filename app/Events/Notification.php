<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Notification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($notification, $userId)
    {
        //

        $this->userId = $userId;
        $this->message = $notification;
        // dd($this->groupId,  $this->message);
        Log::info('[NotifyUser] Constructed for user: ');
    }

    public function broadcastOn()
    {
        Log::info('[NotifyUser] broadcastOn called for: ' . $this->userId);
        return new PrivateChannel('Notification.User.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'user.notification';
    }

    public function broadcastWith()
    {
        Log::info('[NotifyUser] broadcastWith called for: ' . $this->message);
        // return ['count' => 1];
        return [
            'count' => 1,
            'title' => 'ประกาศภัยพิบัติ',
            'message' => 'ดินถล่ม ระดับ 3 พื้นที่ เมืองเชียงราย, แม่จัน, แม่ฟ้าหลวง, แม่สาย'
        ];
    }
}
