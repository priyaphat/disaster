<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

class SampleTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sample-task';
    protected $description = 'Command description';

    public function handle()
    {
        // \Log::info('Run task at ' . now());
    }
}
