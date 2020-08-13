<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\SQLite3Cache;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Container\ContainerInterface;
use SQLite3;

class SQLite3CacheAdapter implements CacheAdapterInterface
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

        $db = new SQLite3($cache_directory . $this->container['registry']->get('flextype.settings.cache.sqlite3.database', 'flextype') . '.db');

        return new SQLite3Cache($db, $this->container['registry']->get('flextype.settings.cache.sqlite3.table', 'flextype'));
    }
}
