<?php
declare(strict_types=1);

namespace App\Contracts\Data\Order;

use Illuminate\Support\Collection;

class OrderItemDataObject
{
    /**
     * @param int $id
     * @param string $name
     * @param float $price
     */
    public function __construct(
        public int $id = 0,
        public string $name = '',
        public float $price = 0,
    ) {
    }

    public static function hydrate(array $orderItems): Collection
    {
        $items = [];
        foreach($orderItems as $item) {
            $items[] = new self(
                (int) $item['id'],
                $item['name'],
                (float) $item['price']
            );
        }
        return collect($items);
    }
}
