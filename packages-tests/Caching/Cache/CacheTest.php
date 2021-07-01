<?php

declare(strict_types=1);

namespace Rector\Tests\Caching\Cache;

use Rector\Caching\Cache;
use Rector\Caching\Enum\CacheKey;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class CacheTest extends AbstractRectorTestCase
{
    private Cache $cache;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = $this->getService(Cache::class);
    }

    protected function tearDown(): void
    {
        $this->cache->clear();
    }

    public function testHasFileChanged(): void
    {
        $this->cache->save('configuration_hash_srv-rector-php', CacheKey::CONFIGURATION_HASH_KEY, 'something');
        $this->assertNotNull(
            $this->cache->load('configuration_hash_srv-rector-php', CacheKey::CONFIGURATION_HASH_KEY)
        );

        $this->cache->save('fc9a36aa55dbb3d22e66e71d4f824cf3f8aba88d', CacheKey::CONFIGURATION_HASH_KEY, 'anything');
        $this->cache->clean('fc9a36aa55dbb3d22e66e71d4f824cf3f8aba88d');

        $this->assertNotNull( // clean actually also cleans other cache key
            $this->cache->load('configuration_hash_srv-rector-php', CacheKey::CONFIGURATION_HASH_KEY)
        );
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config.php';
    }
}
