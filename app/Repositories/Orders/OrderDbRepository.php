<?php
declare(strict_types=1);

namespace App\Repositories\Orders;

use App\Contracts\Data\Order\CreateOrderDataObject;
use App\Contracts\Interfaces\Repositories\OrderRepositoryInterface;
use App\Enums\CurrencyEnum;
use App\Models\Order;
use Illuminate\Database\Connection;

readonly class OrderDbRepository implements OrderRepositoryInterface
{
    public function __construct(
        private Order      $orderModel,
        private Connection $connection
    ){}

    /**
     * @throws \Throwable
     */
    public function createOrder(CreateOrderDataObject $dataObject) : Order
    {
        try {
            $this->connection->beginTransaction();
            $order = $this->orderModel->create([
                'total' => $dataObject->total,
                'currency_id' => CurrencyEnum::fromName($dataObject->currency)->value,
                'transaction_id' => $dataObject->transactionId,
                'user_id' => $dataObject->buyerId
            ]);

            $products = [];
            foreach($dataObject->items as $item) {
                $products[$item->id] = [
                    'total' => $item->price
                ];
            }
            $order->products()->sync($products);
            $this->connection->commit();

            $order->refresh();
            $order->load(['orderProducts', 'orderProducts.product']);

        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        return $order;
    }
}
