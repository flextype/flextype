<?php

namespace Flextype\Cache;

use Doctrine\Common\Cache\FilesystemCache;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Container\ContainerInterface;

class FilesystemAdapter implements CacheAdapterInterface
{
    function __construct(ContainerInterface $flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver(): object
    {
        $cache_directory = PATH['cache'] . '/doctrine/';

        if (! Filesystem::has($cache_directory)) {
            Filesystem::createDir($cache_directory);
        }

        return new FilesystemCache($cache_directory);
    }
}
