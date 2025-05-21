<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\UserPasswordEmail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        //
        Mail::to($event->user->email)->send(new UserPasswordEmail($event->user, $event->password));
    }
}
