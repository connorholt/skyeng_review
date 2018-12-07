<?php declare(strict_types = 1);

namespace src\Integration;

/**
 * Class CacheDecorator
 *
 * @package src\Integration
 */
class CacheDecorator extends DataProviderDecorator
{
    private $cache;

    /**
     * CacheDecorator constructor.
     *
     * @param DataProvider           $provider
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(DataProvider $provider, CacheItemPoolInterface $cache)
    {
        parent::__construct($provider);

        $this->cache = $cache;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    public function get(array $request)
    {
        $cacheKey = $this->getCacheKey($request);
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = $this->provider->get($request);

        $cacheItem
            ->set($result)
            ->expiresAt(
                (new DateTime())->modify('+1 day')
            );
        $this->cache->save($cacheItem);

        return $result;
    }

    public function getCacheKey(array $input)
    {
        return md5(serialize($input));
    }
}
