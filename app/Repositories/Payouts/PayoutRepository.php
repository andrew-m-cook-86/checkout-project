<?php

declare(strict_types=1);

namespace App\Repositories\Payouts;

use App\Contracts\Interfaces\Repositories\PayoutRepositoryInterface;
use App\Events\Payouts\NewInstructionsToProcess;
use App\Libraries\Currency\CurrencyConverter;
use App\Models\Order;
use App\Repositories\Users\UserDbRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Events\Dispatcher;

readonly class PayoutRepository implements PayoutRepositoryInterface
{
    const MAX_INSTRUCTION_AMOUNT = 1000000;

    public function __construct(
        private PayoutDbRepository $dbRepository,
        private UserDbRepository $userDbRepository,
        private CurrencyConverter $currencyConverter,
        private Dispatcher $dispatcher
    )
    {
    }

    /**
     * Create new payouts from an order.
     * A payout will be a total of how much each seller is owed from an order
     * @param Order $order
     * @param ...$props
     * @return void
     */
    public function create(Order $order, ...$props) : void
    {
        $order->load(['orderProducts.product']);
        $sellersToPayOut = [];

        // Builds array of sellers with the total they are owed
        foreach($order->orderProducts as $item) {
            if(isset($sellersToPayOut[$item->product->user_id])) {
                $sellersToPayOut[$item->product->user_id] += (float) $item->total;
            } else {
                $sellersToPayOut[$item->product->user_id] = (float) $item->total;
            }
        }

        foreach($sellersToPayOut as $key => $value) {
            $total = (float) number_format($value, 2, '.', '');
            $this->dbRepository->create($order, total: $total, user: $key);
        }
    }

    public function process()
    {
        // Get all pending payouts and key by the seller id
        $this->dbRepository->processPending($this->mapper());
        $this->dispatcher->dispatch(new NewInstructionsToProcess());
    }

    /**
     * Using closures here because we'll be invoking the chunk function,
     * so as not to load heaps of records into memory at once
     * @return \Closure
     */
    public function mapper(): \Closure
    {
        $vendorQuery = $this->userDbRepository->getVendorModelBuilder();
        $insertQuery = $this->dbRepository->getInstructionInsertQuery();
        $converter = $this->currencyConverter;
        $maxInstructionAmount = self::MAX_INSTRUCTION_AMOUNT;
        return function (Collection $collection) use ($vendorQuery, $insertQuery, $converter, $maxInstructionAmount)
        {
            /**
             * Get sellers and their vendor accounts
             */
            $pendingSellers = $collection->groupBy('user_id');
            $userIds = $pendingSellers->keys();
            $vendors = $vendorQuery($userIds->toArray())->groupBy('user_id')->toArray();

            /**
             * Initialise Instruction Inserts
             */
            $instructionInserts = array_fill_keys($userIds->toArray(), [
                'instructions_total' => 0,
                'instructions' => [
                    [
                        'total' => 0,
                        'payouts' => [],
                    ]
                ]
            ]);

            /**
             * Build Instruction Inserts
             */
            // Cycle through sellers with a pending payout
            foreach($pendingSellers as $sellerPayouts) {

                // Cycle through each seller's individual payout
                foreach($sellerPayouts as $payout){
                    if(!isset($instructionInserts[$payout->user_id]['currency']))
                    {
                        // Quick array access since both instructionInserts and vendors have matching keys
                        $instructionInserts[$payout->user_id]['currency'] =
                            $vendors[$payout->user_id][0]['currency']['name'];
                        $instructionInserts[$payout->user_id]['currency_id'] =
                            $vendors[$payout->user_id][0]['currency']['id'];
                        $instructionInserts[$payout->user_id]['vendor_id'] =
                            $vendors[$payout->user_id][0]['id'];
                    }
                    /**
                     * Adjust Amounts
                     */
                    // if payout is in the vendor's desired currency then just add it to the instruction,
                    // otherwise convert it
                    if($payout->currency->name === $instructionInserts[$payout->user_id]['currency']) {
                        $amount = $payout->total;
                    } else {
                        $amount = $converter->convert(
                            $payout->total,
                            $payout->currency->name,
                            $instructionInserts[$payout->user_id]['currency']
                        );
                    }


                    /**
                     * Determine if payout amounts can be grouped together for an instruction
                     * Or if a payout should be divided into many instructions
                     */
                    if($amount > $maxInstructionAmount) {
                        // max instruction amount is exceeded by the payout amount
                        // divide payout amount among multiple instructions by reducing the max amount from it recursively
                        // until it is below the max instruction threshold
                        // at every iteration create a new instruction
                        $split = function($amount) use (&$split, &$instructionInserts, $maxInstructionAmount, $payout){
                            $amount -= $maxInstructionAmount;
                            if($instructionInserts[$payout->user_id]['instructions_total'] === 0 &&
                                $instructionInserts[$payout->user_id]['instructions'][0]['total'] === 0) {
                                $instructionInserts[$payout->user_id]['instructions'][0] =
                                    [
                                        'total' => $maxInstructionAmount,
                                        'payouts' => [$payout->id],
                                    ];
                            } else {
                                $instructionInserts[$payout->user_id]['instructions'][] =
                                    [
                                        'total' => $maxInstructionAmount,
                                        'payouts' => [$payout->id],
                                    ];
                            }
                            $instructionInserts[$payout->user_id]['instructions_total'] += 1;
                            if($amount < $maxInstructionAmount) {
                                $instructionInserts[$payout->user_id]['instructions'][] =
                                    [
                                        'total' => $amount,
                                        'payouts' => [$payout->id],
                                    ];
                                $instructionInserts[$payout->user_id]['instructions_total'] += 1;
                                return true;
                            } else {
                                return $split($amount);
                            }
                        };
                        $split($amount);
                    } else {
                        $currentInstructionIndex = $instructionInserts[$payout->user_id]['instructions_total'];

                        if($amount +
                            $instructionInserts[$payout->user_id]['instructions'][$currentInstructionIndex]['total'] <
                            $maxInstructionAmount
                        ) {
                            // current instruction amount is still not exceeded when adding payout, so add payout to instruction
                            $instructionInserts[$payout->user_id]['instructions'][$currentInstructionIndex]['total'] += $amount;
                            $instructionInserts[$payout->user_id]['instructions'][$currentInstructionIndex]['payouts'][] = $payout->id;
                        } else {
                            // current instruction amount is exceeded when adding payout, so create new instruction with payout
                            $instructionInserts[$payout->user_id]['instructions_total'] += 1;
                            $instructionInserts[$payout->user_id]['instructions'][] =
                                [
                                    'total' => $amount,
                                    'payouts' => [$payout->id],
                                ];
                        }
                    }
                }
            }

            $insertQuery($instructionInserts);
        };
    }
}
