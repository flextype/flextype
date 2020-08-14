<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\ZendDataCache;
use Psr\Container\ContainerInterface;

class ZendDataCacheCacheAdapter implements CacheAdapterInterface
{
    /**
     * Application
     *
     * @access private
     */
    private $flextype;
    
    public function __construct(ContainerInterface $flextype)
    {
        $this->container = $flextype;
    }

    public function getDriver() : object
    {
        return new ZendDataCache();
    }
}
