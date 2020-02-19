<?php

declare(strict_types=1);

namespace Flextype\Cache;

use Doctrine\Common\Cache\WinCacheCache;
use Psr\Container\ContainerInterface;

class WinCacheAdapter implements CacheAdapterInterface
{
    public function __construct(ContainerInterface $flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver() : object
    {
        return new WinCacheCache();
    }
}
