<?php

namespace Flextype\Cache;

use Doctrine\Common\Cache\ZendDataCache;
use Psr\Container\ContainerInterface;

class ZendDataCacheAdapter implements CacheAdapterInterface
{
    function __construct(ContainerInterface $flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver() : object
    {
        return new ZendDataCache();
    }
}
