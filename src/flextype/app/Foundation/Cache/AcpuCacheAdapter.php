<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\AcpuCache;

class AcpuCacheAdapter implements CacheAdapterInterface
{
    /**
     * Flextype Application
     *
     * @access private
     */
    protected $flextype;

    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver() : object
    {
        return new AcpuCache();
    }
}
