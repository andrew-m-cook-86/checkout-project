<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Order\NewOrderEvent;
use App\Events\Payouts\NewInstructionsToProcess;
use App\Events\Payouts\ProcessInstruction;
use App\Events\UserSignUpEvent;
use App\Listeners\NewOrderEvent\CreateNewPayout;
use App\Listeners\NewOrderEvent\SendBuyerNewOrderNotification;
use App\Listeners\NewOrderEvent\SendVendorsNewOrderNotification;
use App\Listeners\Payout\DispatchNewInstructions;
use App\Listeners\Payout\InstructSeller;
use App\Listeners\SendSignUpNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserSignUpEvent::class => [
            SendSignUpNotification::class,
        ],
        NewOrderEvent::class => [
            CreateNewPayout::class,
            SendBuyerNewOrderNotification::class,
            SendVendorsNewOrderNotification::class
        ],
        NewInstructionsToProcess::class => [
            DispatchNewInstructions::class
        ],
        ProcessInstruction::class => [
            InstructSeller::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
