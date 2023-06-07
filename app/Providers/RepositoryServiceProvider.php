<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Interfaces\Repositories\OrderRepositoryInterface;
use App\Contracts\Interfaces\Repositories\PayoutRepositoryInterface;
use App\Contracts\Interfaces\Repositories\ProductRepositoryInterface;
use App\Libraries\Currency\CurrencyConverter;
use App\Models\Instruction;
use App\Models\Order;
use App\Models\Payout\Payout;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Repositories\Orders\OrderDbRepository;
use App\Repositories\Orders\OrderRepository;
use App\Repositories\Payouts\PayoutDbRepository;
use App\Repositories\Payouts\PayoutRepository;
use App\Repositories\Products\ProductDbRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Users\UserDbRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;
use Illuminate\Contracts\Events\Dispatcher;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Product Repository
        $this->app->bind(ProductRepositoryInterface::class, function($app) {
            $productDbRepository = new ProductDbRepository($app[Product::class]);
            return new ProductRepository($productDbRepository);
        });

        // Order Repository
        $this->app->bind(OrderRepositoryInterface::class, function($app) {
            $orderDbRepository = new OrderDbRepository($app[Order::class], $app[Connection::class]);
            $productDbRepository = new ProductDbRepository($app[Product::class]);
            $currencyConverter = app()->make(CurrencyConverter::class);
            $dispatcher = app()->make(Dispatcher::class);

            return new OrderRepository($orderDbRepository, $productDbRepository, $currencyConverter, $dispatcher);
        });

        // Payout Repository
        $this->app->bind(PayoutRepositoryInterface::class, function($app) {
            $payoutDbRepository = new PayoutDbRepository(
                $app[Payout::class],
                $app[Instruction::class],
                $app[Connection::class]
            );
            $userDbRepository = new UserDbRepository($app[User::class], $app[Vendor::class]);
            $currencyConverter = app()->make(CurrencyConverter::class);
            $dispatcher = app()->make(Dispatcher::class);
            return new PayoutRepository($payoutDbRepository, $userDbRepository, $currencyConverter, $dispatcher);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
