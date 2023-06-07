<?php

namespace App\Listeners\Payout;

use App\Events\Payouts\ProcessInstruction;
use App\Libraries\External\Finance\StripeGateway;
use App\Repositories\Payouts\PayoutDbRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class InstructSeller implements ShouldQueue
{
    public function __construct(
        private readonly PayoutDbRepository $dbRepository,
        private readonly StripeGateway $gateway
    ){}
    /**
     * Handle the event.
     */
    public function handle(ProcessInstruction $event): void
    {
        $token = $this->gateway->transfer($event->instruction);
        if(is_null($token)) {
            $this->dbRepository->markFailedInstruction($event->instruction);
        } else {
            $this->dbRepository->markSuccessfulInstruction($event->instruction);
            // $this->instruction->vendor->notify(Some Email Notification());
        }
    }
}
