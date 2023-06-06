<?php
declare(strict_types=1);

namespace App\Repositories\Orders;

use App\Contracts\Data\Order\CreateOrderDataObject;
use App\Contracts\Interfaces\Repositories\OrderRepositoryInterface;
use App\Events\Order\NewOrderEvent;
use App\Exceptions\OrderException;
use App\Libraries\Currency\CurrencyConverter;
use App\Repositories\Products\ProductDbRepository;
use App\Models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Throwable;

readonly class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private OrderDbRepository   $dbRepository,
        private ProductDbRepository $productDbRepository,
        private CurrencyConverter   $currencyConverter,
        private Dispatcher          $dispatcher
    )
    {

    }

    /**
     * Create the order
     * We want to re-verify that all the figures add up before putting them in a table, as these figures would be
     * used to determine how much a payout would be. We wouldn't use the prices set by the seller in the
     * products table to determine the payout, as if the payout is delayed and the seller updates their prices, then
     * it wouldn't reflect what the buyer originally paid for the item and what would be owed to the seller
     * @throws OrderException|Throwable
     */
    public function createOrder(CreateOrderDataObject $dataObject) : Order
    {
        if(!$this->verifyItemPrice($dataObject))
        {
            throw new OrderException('Item price does not match records');
        }

        if(!$this->verifyTotalPrice($dataObject))
        {
            throw new OrderException('Total price does not add up');
        }

        $order = $this->dbRepository->createOrder($dataObject);

        $this->dispatcher->dispatch(new NewOrderEvent($order));

        return $order;
    }

    /**
     * Verifies that price of items in cart matches those in DB or those converted via currency exchange
     * @param CreateOrderDataObject $dataObject
     * @return bool
     */
    private function verifyItemPrice(CreateOrderDataObject $dataObject) : bool
    {
        $itemsVerified = 0;
        $items = $dataObject->items->keyBy('id')->toArray();
        $products = $this->productDbRepository->listProductsById(array_keys($items))->keyBy('id');
        // Compare prices between array of items sent in via API vs those same items from the DB
        // Two arrays share same keys, so look up is quicker
        foreach($products as $product) {
            // If item currency matches the buyer selected currency, then only need to compare prices
            if(
                $product->currency->name === $dataObject->currency &&
                $product->price === $items[$product->id]->price)
            {
                $itemsVerified++;
                continue;
            }
            // If item currency does not match buyer selected currency, then will need perform conversion to do match
            // Ideally conversion was done previously and presented to customer during checkout, so we wouldn't be
            // Calling a third party api in this request as the results of the previous conversion would be held somewhere
            // redis in this case
            $conversion = $this->currencyConverter->convert(
                $product->price,
                $product->currency->name,
                $dataObject->currency);
            if($conversion === $items[$product->id]->price) {
                $itemsVerified++;
            }
        }

        return $itemsVerified === count($items);
    }

    /**
     * Verifies that total price matches total of items in cart
     * @param CreateOrderDataObject $dataObject
     * @return bool
     */
    private function verifyTotalPrice(CreateOrderDataObject $dataObject) : bool
    {
        $sum = 0;
        $total = $dataObject->total;
        foreach($dataObject->items as $item) {
            $sum += $item->price;
        }

        return $total === (float) number_format($sum, 2, '.', '');
    }
}
