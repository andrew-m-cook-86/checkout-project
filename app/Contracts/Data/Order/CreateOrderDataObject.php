<?php

declare(strict_types=1);

namespace App\Contracts\Data\Order;

use App\Contracts\Interfaces\DataObject;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

readonly class CreateOrderDataObject implements DataObject
{
    /**
     * @param string $transactionId
     * @param string $currency
     * @param float $total
     * @param Collection $items
     * @param int $buyerId
     */
    public function __construct(
        public string $transactionId = '',
        public string $currency = '',
        public float $total = 0,
        public Collection $items = new Collection(),
        public int $buyerId = 0
    ) {
    }

    /**
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): static
    {
        return new self(
            $request->input('transaction_id'),
            $request->input('currency'),
            (float) $request->input('total'),
            OrderItemDataObject::hydrate($request->input('items')),
            $request->user()->id,
        );
    }
}
