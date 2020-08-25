<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\ArrayCache;

class ArrayCacheAdapter implements CacheAdapterInterface
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
        return new ArrayCache();
    }
}
