<?php

namespace App\Listeners;

use App\Events\UserSignUpEvent;
use App\Notifications\SignUpNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSignUpNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly SignUpNotification $signUpNotification){}

    /**
     * Handle the event.
     */
    public function handle(UserSignUpEvent $event): void
    {
        if (!$event->user->hasVerifiedEmail()) {
            $event->user->notify($this->signUpNotification);
        }
    }
}
