<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SendNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $notification;

    /**
     * Create a new notification instance.
     */
    public function __construct($notification)
    {
        //
         Log::info("test notification broadcast");
        $this->notification = $notification;
    }

    /**
     * Define the channels for the notification.
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    /**
     * Define the data to be sent in the broadcast.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->notification['title'],
            'description' => $this->notification['description'],
            'format' => $this->notification['format'],
        ]);
    }


    /**
     * Optional: Customize the broadcast event name.
     */
    public function broadcastAs()
    {
         Log::info("broadcastAs");
        return 'user.notification';
    }
}
