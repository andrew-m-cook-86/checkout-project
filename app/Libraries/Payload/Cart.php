<?php

declare(strict_types=1);

namespace App\Libraries\Payload;

use App\Enums\CurrencyEnum;
use App\Libraries\Currency\CurrencyConverter;
use App\Models\Order;
use App\Models\Product;

/**
 * This is just a junk class, and wouldn't live in a real application.
 * It's just a substitute for the lack of a real UI / Shopping Cart
 * Generates a payload to be sent to test the order endpoint. Currency to pay in and items in cart are variable
 * Invoked via artisan command
 */
class Cart
{
    const DEFAULT_ITEMS_COUNT = 5;
    const MAX_ITEMS_COUNT = 15;
    const DEFAULT_CURRENCY = CurrencyEnum::USD->value;

    public function __construct(
        readonly private Product           $product,
        readonly private CurrencyConverter $converter,
        readonly private Order             $order
    )
    {

    }

    /**
     * Generate a payload
     * @param int $number
     * @param string $selectedCurrency
     * @return string
     */
    public function generate(int $number, string $selectedCurrency): string
    {
        // Get random products
        $products = $this->product
            ->newQuery()
            ->select('name', 'price', 'currency_id', 'id')
            ->with('currency:name,id')
            ->inRandomOrder()
            ->limit($number)
            ->get();

        // Calculate price of items (and convert to new currency if required)
        $total = 0;
        $products->transform(function (Product $item) use ($selectedCurrency, &$total) {
            $defaultCurrency = $item->currency->name;
            $defaultPrice = $item->price;
            if($defaultCurrency !== $selectedCurrency) {
                $item->price = $this->converter->convert($defaultPrice, $defaultCurrency, $selectedCurrency);
            }
            unset($item->currency);
            unset($item->currency_id);
            $total += $item->price;
            return $item;
        });

        // Return cart with mocked vendor transaction id
        $cart = [
            'currency' => $selectedCurrency,
            'total' => number_format($total, 2, '.', ''),
            'items' => $products,
            'transaction_id' => $this->order->factory()->make(['user_id' => 9999999])->transaction_id,
        ];

        return json_encode($cart);
    }
}
