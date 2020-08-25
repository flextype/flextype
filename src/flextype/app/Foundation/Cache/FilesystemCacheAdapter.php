<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\FilesystemCache;
use Flextype\Component\Filesystem\Filesystem;

class FilesystemCacheAdapter implements CacheAdapterInterface
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
        $cache_directory = PATH['cache'] . '/doctrine/';

        if (! Filesystem::has($cache_directory)) {
            Filesystem::createDir($cache_directory);
        }

        return new FilesystemCache($cache_directory);
    }
}
