<?php

namespace App\Listeners\NewOrderEvent;

use App\Events\Order\NewOrderEvent;
use App\Notifications\NewOrderSellerNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class SendVendorsNewOrderNotification implements ShouldQueue
{
    /**
     * @param NewOrderSellerNotification $notification
     */
    public function __construct(private readonly NewOrderSellerNotification $notification){}

    /**
     * Handle the event.
     */
    public function handle(NewOrderEvent $event): void
    {
        $notified = [];
        $event->order->load(['products', 'products.user']);
        foreach($event->order->products as $product) {
            if(!in_array($product->user->id, $notified)) {
                $product->user->notify($this->notification);
                $notified[] = $product->user->id;
            }
        }
    }
}
