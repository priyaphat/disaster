<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotificationTask extends Command
{
    protected $signature = 'app:notification-task';
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Notification::doesntHave('notification_users')->delete();
        Log::info("Delete notifications that doesn't have users.");
    }
}
