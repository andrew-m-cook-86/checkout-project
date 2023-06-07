<?php

namespace App\Listeners\Payout;

use App\Events\Payouts\NewInstructionsToProcess;
use App\Events\Payouts\ProcessInstruction;
use App\Repositories\Payouts\PayoutDbRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;

readonly class DispatchNewInstructions implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private PayoutDbRepository $dbRepository,
        private Dispatcher $dispatcher
    ){}

    /**
     * Handle the event.
     */
    public function handle(NewInstructionsToProcess $event): void
    {
        $this->dbRepository->listRecentInstructions($this->dispatchProcessEvent());
    }

    private function dispatchProcessEvent(): \Closure
    {
        $dispatcher = $this->dispatcher;
        return function (Collection $collection) use ($dispatcher)
        {
            foreach($collection as $model) {
                $dispatcher->dispatch(new ProcessInstruction($model));
            }
        };
    }
}
