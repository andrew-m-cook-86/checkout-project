<?php

namespace App\Listeners\NewOrderEvent;

use App\Events\Order\NewOrderEvent;
use App\Notifications\NewOrderBuyerNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class SendBuyerNewOrderNotification implements ShouldQueue
{
    /**
     * @param NewOrderBuyerNotification $notification
     */
    public function __construct(private readonly NewOrderBuyerNotification $notification){}

    /**
     * Handle the event.
     */
    public function handle(NewOrderEvent $event): void
    {
        $event->order->user->notify($this->notification);
    }
}
