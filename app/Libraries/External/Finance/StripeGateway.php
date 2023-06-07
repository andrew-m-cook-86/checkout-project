<?php
declare(strict_types=1);

namespace App\Libraries\External\Finance;

use App\Models\Instruction;
use Illuminate\Config\Repository;
use Stripe\Transfer;

readonly class StripeGateway
{
    public function __construct(
        private Transfer $stripe,
        private Repository $config,
        private Instruction $instruction
    ){}

    /**
     * Transfer fund to vendor's stripe account -- obviously doesn't work
     * @param Instruction $instruction
     * @return string|null
     */
    public function transfer(Instruction $instruction): string|null
    {
        try {
            if($this->config->get('payment.providers.stripe.key') === 'pk_test') {
                $resp = $this->instruction::factory()->make(['user_id' => 99999])->transaction_id;
            } else {
                $transfer = $this->stripe::create(
                    [
                        'destination' => $instruction->vendor->store_id,
                        'amount' => $instruction->amount,
                        'currency' => strtolower($instruction->currency->name),
                    ],
                    [
                      'api_key' => $this->config->get('payment.providers.stripe.key')
                    ]);
                $resp = $transfer->getLastResponse()->body;
            }
        } catch (\Exception $e) {
            // Log $e
            return null;
        }

        return $resp;
    }
}
