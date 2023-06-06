<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CurrencyEnum;
use App\Libraries\Payload\Cart;
use App\Repositories\Payouts\PayoutRepository;
use Illuminate\Console\Command;

class ProcessPayouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-payout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(PayoutRepository $repository)
    {
        $repository->process();
    }
}
