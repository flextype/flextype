<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Cache\Common\AcpuCache;
use Psr\Container\ContainerInterface;

class AcpuCacheAdapter implements CacheAdapterInterface
{
    public function __construct(ContainerInterface $flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver() : object
    {
        return new AcpuCache();
    }
}
