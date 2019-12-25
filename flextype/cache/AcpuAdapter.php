<?php

declare(strict_types=1);

namespace Flextype\Cache;

use Psr\Container\ContainerInterface;
use Doctrine\Cache\Common\AcpuCache;

class AcpuAdapter implements CacheAdapterInterface
{
    function __construct(ContainerInterface $flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver() : object
    {
        return new AcpuCache();
    }
}
