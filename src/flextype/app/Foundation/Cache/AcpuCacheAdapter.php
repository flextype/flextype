<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\AcpuCache;

class AcpuCacheAdapter implements CacheAdapterInterface
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
        return new AcpuCache();
    }
}
