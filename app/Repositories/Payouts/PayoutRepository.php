<?php

declare(strict_types=1);

namespace App\Repositories\Payouts;

use App\Contracts\Interfaces\Repositories\PayoutRepositoryInterface;
use App\Models\Order;

readonly class PayoutRepository implements PayoutRepositoryInterface
{
    public function __construct(private PayoutDbRepository $dbRepository)
    {
    }

    /**
     * Create new payouts from an order.
     * A payout will be a total of how much each seller is owed from an order
     * @param Order $order
     * @param ...$props
     * @return void
     */
    public function create(Order $order, ...$props) : void
    {
        $order->load(['orderProducts.product']);
        $sellersToPayOut = [];

        // Builds array of sellers with the total they are owed
        foreach($order->orderProducts as $item) {
            if(isset($sellersToPayOut[$item->product->user_id])) {
                $sellersToPayOut[$item->product->user_id] += (float) $item->total;
            } else {
                $sellersToPayOut[$item->product->user_id] = (float) $item->total;
            }
        }

        foreach($sellersToPayOut as $key => $value) {
            $total = (float) number_format($value, 2, '.', '');
            $this->dbRepository->create($order, total: $total, user: $key);
        }
    }

    public function process()
    {
        $pendingPayouts = $this->dbRepository->listPending()->groupBy('user_id');
        $userIds = $pendingPayouts->keys();
        dd($userIds);
        foreach($pendingPayouts as $pendingPayout) {

        }
    }
}
