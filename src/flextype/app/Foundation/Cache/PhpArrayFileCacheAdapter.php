<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Flextype\Component\Filesystem\Filesystem;

class PhpArrayFileCacheAdapter implements CacheAdapterInterface
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

        return new PhpArrayFileCache($cache_directory);
    }
}
