<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\FilesystemCache;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Container\ContainerInterface;

class FilesystemCacheAdapter implements CacheAdapterInterface
{
    /**
     * Dependency Container
     *
     * @access private
     */
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
