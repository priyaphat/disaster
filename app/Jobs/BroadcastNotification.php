<?php

namespace App\Jobs;

use App\Events\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class BroadcastNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $notifications // array ของ payloads
    ) {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        foreach ($this->notifications as $noti) {
            broadcast(new Notification($noti['user_id'], 'test1'));
        }
    }
}
