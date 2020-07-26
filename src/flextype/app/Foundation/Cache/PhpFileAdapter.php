<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\PhpFileCache;
use Psr\Container\ContainerInterface;
use Flextype\Component\Filesystem\Filesystem;

class PhpFileAdapter implements CacheAdapterInterface
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

        return new PhpFileCache($cache_directory);
    }
}
