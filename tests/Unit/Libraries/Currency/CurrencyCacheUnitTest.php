<?php

declare(strict_types=1);

namespace Tests\Unit\Libraries\Currency;

use App\Libraries\Currency\CurrencyCache;
use Illuminate\Cache\CacheManager;
use PHPUnit\Framework\TestCase;
use STS\Backoff\Backoff;
use Mockery as m;

class CurrencyCacheUnitTest  extends TestCase
{
    /**
     * @var CurrencyCache
     */
    private $currencyCache;

    /**
     * @var CacheManager|(CacheManager&m\LegacyMockInterface)|(CacheManager&m\MockInterface)|m\LegacyMockInterface|m\MockInterface
     */
    private $cacheManagerMock;

    public function setUp(): void
    {
        $this->cacheManagerMock = m::mock(CacheManager::class);
        $this->currencyCache = new CurrencyCache($this->cacheManagerMock);

        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * @test
     * @group unit
     * @return void
     */
    public function it_should_check_exists()
    {
        $this->cacheManagerMock->shouldReceive('has')
            ->once()
            ->with('string')
            ->andReturnTrue();
        $this->assertTrue($this->currencyCache->exists('string'));
    }

    /**
     * @test
     * @group unit
     * @return void
     */
    public function it_should_retrieve_and_cast_to_float()
    {
        $return = '12345';
        $this->cacheManagerMock->shouldReceive('get')
            ->once()
            ->with('string')
            ->andReturn($return);
        $this->assertEquals((float) $return, $this->currencyCache->retrieve('string'));
    }

    /**
     * @test
     * @doesNotPerformAssertions
     * @group unit
     * @return void
     */
    public function it_should_cache()
    {
        $this->cacheManagerMock->shouldReceive('set')
            ->once()
            ->with('key', 12345.0, 3600)
            ->andReturnTrue();
       $this->currencyCache->cache(12345.0, 'key');
    }

    /**
     * @test
     * @group unit
     * @return void
     */
    public function it_should_generate_key()
    {
        $return = '12345.79:from:to';
        $this->assertEquals($return, $this->currencyCache->generateKey(
            12345.79,
            'from',
            'to'
        ));
    }
}