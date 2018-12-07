<?php declare(strict_types = 1);

namespace src\Integration;

/**
 * Class CacheDecorator
 *
 * @package src\Integration
 */
abstract class DataProviderDecorator implements Providerable
{
    /**
     * @var DataProvider
     */
    protected $provider;

    /**
     * DataProviderDecorator constructor.
     *
     * @param DataProvider $provider
     */
    public function __construct(DataProvider $provider)
    {
        $this->provider = $provider;
    }
}