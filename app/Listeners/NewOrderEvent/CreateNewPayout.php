<?php

namespace App\Listeners\NewOrderEvent;

use App\Contracts\Interfaces\Repositories\PayoutRepositoryInterface;
use App\Events\Order\NewOrderEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class CreateNewPayout implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(private PayoutRepositoryInterface $payoutRepository){}

    /**
     * Handle the event.
     */
    public function handle(NewOrderEvent $event): void
    {
        $this->payoutRepository->create($event->order);
    }
}
