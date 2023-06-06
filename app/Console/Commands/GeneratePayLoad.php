<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CurrencyEnum;
use App\Libraries\Payload\Cart;
use Illuminate\Console\Command;

class GeneratePayLoad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-payload
                            {--items= : The number of items to add to the cart}
                            {--currency= : The currency code to be charged in}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Cart $order)
    {
        $items = (int) $this->option('items') ?? Cart::DEFAULT_ITEMS_COUNT;
        $currency = $this->option('currency') ?? Cart::DEFAULT_CURRENCY;
        if(!$this->validate($items, $currency)){
            return;
        }
        $this->output->writeLn($order->generate($items, strtoupper($currency)));
    }

    private function validate($items, $currency): bool
    {
        if($items > Cart::MAX_ITEMS_COUNT || $items < 1) {
            $this->output->error("Items must be between 1 and " . Cart::MAX_ITEMS_COUNT);
            return false;
        }

        if(!CurrencyEnum::exists($currency)) {
            $this->output->error("Currency must be one of " . implode(", ", CurrencyEnum::names()));
            return false;
        }

        return true;
    }
}
