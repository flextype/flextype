<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Psr\Container\ContainerInterface;

class ArrayCacheAdapter implements CacheAdapterInterface
{
    /**
     * Dependency Container
     *
     * @access private
     */
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getDriver() : object
    {
        return new ArrayCache();
    }
}
