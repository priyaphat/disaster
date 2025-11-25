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

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
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
            'title' => $this->data['notification']['title'],
            'description' => $this->data['notification']['description'],
            'format' => $this->data['notification']['format'],
            'event_type' => $this->data['event_type'],
            'event_action' => $this->data['event_action'],
            'entity_type' => $this->data['entity_type'],
            'entity_id' => $this->data['entity_id'],
            'related_data' => $this->data['related_data'],
            'show_details' => $this->data['show_details'],
        ]);
    }


    /**
     * Optional: Customize the broadcast event name.
     */
    public function broadcastAs()
    {
        return 'user.notification';
    }
}
