<?php

namespace App\Listeners;

use App\Enums\ApprovalStatus;
use App\Events\AdminRegistered;
use App\Events\UserRegistered;
use App\Mail\RegisterEmail;
use App\Mail\UserPasswordEmail;
use App\Models\UserApproval as Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\API\LineController;

class UserEventSubscriber
{
    // Handle the AdminRegistered event.
    public function onAdminRegistred(AdminRegistered $event)
    {
        // Create an approval
        $data = [
            'user_id' => $event->user->id,
            'is_approved' => ApprovalStatus::APPROVED->value,
            'approved_by' => Auth::id() ?? null,
            'approved_at' => $event->user->created_at,
        ];
        $this->createApproval($data);
        // Send email to user
        Mail::to($event->user->email)->send(new UserPasswordEmail($event->user, $event->password));
    }

    // Handle the UserRegistered event
    public function onUserRegistred(UserRegistered $event)
    {
        // Create an approval
        $data = [
            'user_id' => $event->user->id,
            'is_approved' => ApprovalStatus::PENDING->value
        ];
        $this->createApproval($data);
        // Send email to user
        Mail::to($event->user->email)->send(new RegisterEmail($event->user));
        (new LineController)->LineNotification(null, LineController::User_register);
    }


    public function createApproval(array $data): void
    {
        Approval::create($data);
    }

    public function subscribe($events)
    {
        return [
            AdminRegistered::class => 'onAdminRegistred',
            UserRegistered::class => 'onUserRegistred',
        ];
    }
}
