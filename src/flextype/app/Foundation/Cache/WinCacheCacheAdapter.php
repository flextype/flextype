<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\WinCacheCache;
use Psr\Container\ContainerInterface;

class WinCacheCacheAdapter implements CacheAdapterInterface
{
    /**
     * Flextype Dependency Container
     *
     * @access private
     */
    private $flextype;
    
    public function __construct(ContainerInterface $flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver() : object
    {
        return new WinCacheCache();
    }
}
