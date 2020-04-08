<?php

declare(strict_types=1);

namespace Flextype\Cache;

use Doctrine\Common\Cache\MemcachedCache;
use Memecached;
use Psr\Container\ContainerInterface;

class MemcachedAdapter implements CacheAdapterInterface
{
    public function __construct(ContainerInterface $flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver() : object
    {
        $memcached = new Memecached();
        $memcached->addServer(
            $this->flextype['registry']->get('flextype.cache.memcached.server', 'localhost'),
            $this->flextype['registry']->get('flextype.cache.memcache.port', 11211)
        );

        $driver = new MemcachedCache();
        $driver->setMemcached($memcached);

        return $driver;
    }
}
