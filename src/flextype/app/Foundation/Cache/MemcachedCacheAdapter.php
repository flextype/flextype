<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\MemcachedCache;
use Memecached;

class MemcachedCacheAdapter implements CacheAdapterInterface
{
    /**
     * Flextype Application
     *
     * @access private
     */
    protected $flextype;

    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver() : object
    {
        $memcached = new Memecached();
        $memcached->addServer(
            $this->flextype->container('registry')->get('flextype.settings.cache.memcached.server', 'localhost'),
            $this->flextype->container('registry')->get('flextype.settings.cache.memcache.port', 11211)
        );

        $driver = new MemcachedCache();
        $driver->setMemcached($memcached);

        return $driver;
    }
}
