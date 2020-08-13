<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\MemcachedCache;
use Memecached;
use Psr\Container\ContainerInterface;

class MemcachedCacheAdapter implements CacheAdapterInterface
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
        $memcached = new Memecached();
        $memcached->addServer(
            $this->container['registry']->get('flextype.settings.cache.memcached.server', 'localhost'),
            $this->container['registry']->get('flextype.settings.cache.memcache.port', 11211)
        );

        $driver = new MemcachedCache();
        $driver->setMemcached($memcached);

        return $driver;
    }
}
