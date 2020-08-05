<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Flextype\Component\Filesystem\Filesystem;
use Psr\Container\ContainerInterface;

class PhpArrayFileCacheAdapter implements CacheAdapterInterface
{
    /**
     * Flextype Dependency Container
     *
     * @access private
     */
    private $flextype;
    
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
