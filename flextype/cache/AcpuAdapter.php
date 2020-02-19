<?php

declare(strict_types=1);

namespace Flextype\Cache;

use Doctrine\Cache\Common\AcpuCache;
use Psr\Container\ContainerInterface;

class AcpuAdapter implements CacheAdapterInterface
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
