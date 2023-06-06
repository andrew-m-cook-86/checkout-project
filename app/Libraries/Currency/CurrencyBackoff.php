<?php
declare(strict_types=1);

namespace App\Libraries\Currency;

use STS\Backoff\Backoff;

class CurrencyBackoff
{
    const BACKOFF_STRATEGY = 'constant';
    const BACKOFF_ATTEMPTS = 10;
    const BACKOFF_JITTER = true;

    private Backoff|null $backoff = null;

    /**
     * Returns the same instance of backoff for currency library
     * @return Backoff
     */
    public function instance(): Backoff
    {
        if(!is_null($this->backoff)){
            return $this->backoff;
        }

        return $this->init();
    }

    /**
     * Creates a new Backoff for the currency library
     * @return Backoff
     */
    private function init(): Backoff
    {
        $this->backoff = new Backoff();
        return $this->backoff->setStrategy(self::BACKOFF_STRATEGY)
            ->setMaxAttempts(self::BACKOFF_ATTEMPTS)
            ->setJitter(self::BACKOFF_JITTER);
    }
}
