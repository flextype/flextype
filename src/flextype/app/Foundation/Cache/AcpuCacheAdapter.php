<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\AcpuCache;

class AcpuCacheAdapter implements CacheAdapterInterface
{
    public function __construct()
    {

    }

    public function getDriver() : object
    {
        return new AcpuCache();
    }
}
