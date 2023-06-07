<?php

declare(strict_types=1);

namespace App\Repositories\Payouts;

use App\Contracts\Interfaces\Repositories\PayoutRepositoryInterface;
use App\Enums\InstructionStatusEnum;
use App\Enums\PayoutStatusEnum;
use App\Models\Instruction;
use App\Models\Order;
use App\Models\Payout\Payout;
use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;

readonly class PayoutDbRepository implements PayoutRepositoryInterface
{
    public function __construct(
        private Payout $payoutModel,
        private Instruction $instructionModel,
        private Connection $connection,
    )
    {
    }

    /**
     * @param Order $order
     * @param ...$props
     * @return void
     */
    public function create(Order $order, ...$props) : void
    {
        $this->payoutModel->create([
            'order_id' => $order->id,
            'user_id' => $props['user'],
            'total' => $props['total'],
            'currency_id' => $order->currency_id,
            'status' => PayoutStatusEnum::PENDING->value,
        ]);
    }

    /**
     * @param \Closure $mapper
     * @return bool
     */
    public function processPending(\Closure $mapper): bool
    {
        return $this->payoutModel
            ->newQuery()
            ->where('status', PayoutStatusEnum::PENDING->value)
            ->with('currency')
            ->chunk(100, $mapper);
    }

    public function listRecentInstructions(\Closure $dispatcher): bool
    {
        return $this->instructionModel
            ->newQuery()
            ->whereNull('status')
            ->with(['payouts', 'vendor', 'currency'])
            ->chunk(100, $dispatcher);
    }

    /**
     * Marks an instruction as failed.
     * Marks an instructions payouts as failed
     * @param Instruction $instruction
     * @return void
     */
    public function markFailedInstruction(Instruction $instruction)
    {
        $instruction->load('payouts');
        foreach($instruction->payouts as $payout){
            $payout->update([
                'status' => PayoutStatusEnum::FAILED->value,
                'last_attempted_at' => Carbon::now()
            ]);
        };
        $instruction->update([
            'status' => InstructionStatusEnum::FAILED->value,
            'last_attempted_at' => Carbon::now()
        ]);
    }

    /**
     * Marks an instruction as completed.
     * Marks an instructions payouts as completed
     * If the payouts also were divided in other instructions that are still incomplete,
     * then those payouts are marked as partial
     * @param Instruction $instruction
     * @return void
     */
    public function markSuccessfulInstruction(Instruction $instruction)
    {
        $instruction->load('payouts.instructions');
        foreach($instruction->payouts as $payout){
            if(
                $payout->instructions->where('id', '!=', $instruction->id)
                    ->where('status', '!=', InstructionStatusEnum::COMPLETED->value)
                    ->count() === 0) {
                $payout->update([
                    'status' => PayoutStatusEnum::COMPLETED->value,
                    'completed_at' => Carbon::now()
                ]);
            } else {
                $payout->update([
                    'status' => PayoutStatusEnum::PARTIAL->value,
                    'last_attempted_at' => Carbon::now()
                ]);
            }
        };
        $instruction->update([
            'status' => InstructionStatusEnum::COMPLETED->value,
            'completed_at' => Carbon::now()
        ]);
    }

    /**
     * @return \Closure
     */
    public function getInstructionInsertQuery() : \Closure
    {
        return function (array $instructions) {
            foreach($instructions as $instructionData) {
                foreach($instructionData['instructions'] as $data) {
                    // could reduce queries if necessary by building large insert array and creating
                    // many at once
                    try {
                        $this->connection->beginTransaction();
                        $instruction = $this->instructionModel->create([
                            'vendor_id' => $instructionData['vendor_id'],
                            'currency_id' => $instructionData['currency_id'],
                            'amount' => $data['total'],
                            'status' => null
                        ]);

                        $pivot = array_fill_keys($data['payouts'], [
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]
                        );

                        $instruction->payouts()->attach($pivot);
                        $this->connection->commit();
                    } catch (\Exception $e) {
                        // TODO: add logging
                        $this->connection->rollBack();
                    }
                }
            }
        };
    }
}
