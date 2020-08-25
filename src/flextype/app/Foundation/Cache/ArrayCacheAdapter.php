<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\ArrayCache;

class ArrayCacheAdapter implements CacheAdapterInterface
{
    public function getDriver() : object
    {
        return new ArrayCache();
    }
}
