<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Flextype\App\Foundation\Cache\PhpArrayFileCache;
use Psr\Container\ContainerInterface;
use Flextype\Component\Filesystem\Filesystem;

class PhpArrayFileCacheAdapter implements CacheAdapterInterface
{
    public function __construct(ContainerInterface $flextype)
    {
        $this->flextype = $flextype;
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
