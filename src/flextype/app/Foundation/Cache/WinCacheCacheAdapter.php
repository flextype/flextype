<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\WinCacheCache;

class WinCacheCacheAdapter implements CacheAdapterInterface
{
    /**
     * Flextype Application
     *
     * @access private
     */


    public function __construct()
    {
        
    }

    public function getDriver() : object
    {
        return new WinCacheCache();
    }
}
