<?php

declare(strict_types=1);

namespace App\Repositories\Payouts;

use App\Contracts\Interfaces\Repositories\PayoutRepositoryInterface;
use App\Enums\PayoutStatusEnum;
use App\Models\Order;
use App\Models\Payout\Payout;
use Illuminate\Database\Eloquent\Collection;

readonly class PayoutDbRepository implements PayoutRepositoryInterface
{
    public function __construct(private Payout $payoutModel)
    {
    }

    /**
     * @param Order $order
     * @param ...$props
     * @return void
     */
    public function create(Order $order, ...$props) : void
    {
        $this->payoutModel->create([
            'order_id' => $order->id,
            'user_id' => $props['user'],
            'total' => $props['total'],
            'currency_id' => $order->currency_id,
            'status' => PayoutStatusEnum::PENDING->value,
        ]);
    }

    /**
     * @return Collection
     */
    public function listPending(): Collection
    {
        return $this->payoutModel
            ->newQuery()
            ->where('status', PayoutStatusEnum::PENDING->value)
            ->get();
    }
}
