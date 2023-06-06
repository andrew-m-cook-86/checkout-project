<?php

declare(strict_types=1);

namespace App\Libraries\Currency;

use AmrShawky\CurrencyFactory;

/**
 * Performs currency conversions via an exchange host api
 * Uses caching to reduce calls (and reduce dependency on api uptime)
 * Uses backoff in case of call failure
 */
readonly class CurrencyConverter
{
    public function __construct(
        private CurrencyCache   $cacheManager,
        private CurrencyFactory $currencyFactory,
        private CurrencyBackoff $backoff
    ){}

    public function convert($amount, $from, $to): float
    {
        $amount = (float) $amount;
        $key = $this->cacheManager->generateKey($amount, $from, $to);
        if($this->cacheManager->exists($key)) {
            return $this->cacheManager->retrieve($key);
        }

        $conversion = $this->backoff->instance()->run(function() use($from, $to, $amount) {
            return $this->currencyFactory->convert()
                ->from($from)
                ->to($to)
                ->amount($amount)
                ->get();
        });

        $newAmount = (float) number_format($conversion, 2);
        $this->cacheManager->cache($newAmount, $key);
        return $newAmount;
    }
}
