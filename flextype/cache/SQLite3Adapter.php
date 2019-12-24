<?php

namespace Flextype\Cache;

use Flextype\Component\Filesystem\Filesystem;
use SQLite3;
use Doctrine\Common\Cache\SQLite3Cache;
use Psr\Container\ContainerInterface;

class SQLite3Adapter implements CacheAdapterInterface
{
    function __construct(ContainerInterface $flextype)
    {
        $this->flextype = $flextype;
    }

    public function getDriver() : object
    {
        $cache_directory = PATH['cache'] . '/doctrine/';

        if (! Filesystem::has($cache_directory)) {
            Filesystem::createDir($cache_directory);
        }

        $db = new SQLite3($cache_directory . $this->flextype['registry']->get('settings.cache.sqlite3.database', 'flextype') . '.db');

        return new SQLite3Cache($db, $this->flextype['registry']->get('settings.cache.sqlite3.table', 'flextype'));
    }
}
