<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\MemcachedCache;
use Memecached;

class MemcachedCacheAdapter implements CacheAdapterInterface
{
    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {

    }

    public function getDriver() : object
    {
        $memcached = new Memecached();
        $memcached->addServer(
            flextype('registry')->get('flextype.settings.cache.memcached.server', 'localhost'),
            flextype('registry')->get('flextype.settings.cache.memcache.port', 11211)
        );

        $driver = new MemcachedCache();
        $driver->setMemcached($memcached);

        return $driver;
    }
}
