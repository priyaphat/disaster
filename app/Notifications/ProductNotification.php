<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ProductNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**Define the channels for the notification.*/

    public function via($notifiable)
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'action' => 'refresh',
        ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'action' => 'refresh',
        ];
    }

    public function broadcastAs(): string
    {
        return 'page.refresh';
    }
}
