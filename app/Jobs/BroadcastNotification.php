<?php

namespace App\Jobs;

use App\Events\Notification;
use App\Models\NotificationUser;
use App\Notifications\SendNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class BroadcastNotification implements ShouldQueue
{

    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Collection $users, public $data
    ) {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        $rows = $this->users->map(fn($user) => [
            'notification_id' => $this->data->id,
            'user_id' => $user->id,
            'is_read' => false
        ])->toArray();

        NotificationUser::upsert($rows, ['notification_id', 'user_id'], ['is_read']);
        foreach ($this->users as $user) {
            $user->notify(new SendNotification($this->data->notification));
        }
    }
}
