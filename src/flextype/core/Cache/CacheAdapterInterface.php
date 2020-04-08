<?php

declare(strict_types=1);

namespace Flextype\Cache;

use Psr\Container\ContainerInterface;

interface CacheAdapterInterface
{
    /**
     * Injects the dependency container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container);

    /**
     * Returns the cache driver object
     */
    public function getDriver() : object;
}
