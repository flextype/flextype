<?php

declare(strict_types=1);

namespace Flextype\Cache;

use Psr\Container\ContainerInterface;

interface CacheAdapterInterface
{
    /**
     * Injects the dependency container
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return void
     */
    function __construct(ContainerInterface $container);

    /**
     * Returns the cache driver object
     *
     * @return object
     */
    public function getDriver() : object;
}
