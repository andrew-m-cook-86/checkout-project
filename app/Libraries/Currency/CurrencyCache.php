<?php

declare(strict_types=1);

namespace App\Libraries\Currency;

use Illuminate\Cache\CacheManager;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Handles caching for currency library
 * Reduces calls made to exchange rate api
 */
class CurrencyCache
{
    const CURRENCY_CONVERSION_TTL = 3600;

    public function __construct(
        readonly private CacheManager $cacheManager
    ){}

    /**
     * Determines if conversion was recently made
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->cacheManager->has($key);
    }

    /**
     * Retrieve exchanged rate if less than hour old
     * @param string $key
     * @return float
     */
    public function retrieve(string $key): float
    {
        return (float) $this->cacheManager->get($key);
    }

    /**
     * Store exchanged rate for one hour
     * @param float $newAmount
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    public function cache(float $newAmount, string $key): void
    {
        $this->cacheManager->set($key, $newAmount, self::CURRENCY_CONVERSION_TTL);
    }

    /**
     * Form key for k/v store
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return string
     */
    public function generateKey(float $amount, string $from, string $to): string
    {
        return $amount . ":" . $from . ":" . $to;
    }
}
