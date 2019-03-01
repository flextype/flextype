<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Registry\Registry;
use \Doctrine\Common\Cache as DoctrineCache;

class Cache
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Unique cache key
     *
     * @var string Cache key.
     */
    private $key;

    /**
     * Lifetime
     *
     * @var int Lifetime.
     */
    private $lifetime;

    /**
     * Current time
     *
     * @var int Current time.
     */
    private $now;

    /**
     * Cache Driver
     *
     * @var DoctrineCache
     */
    private $driver;

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;

        // Create Cache Directory
        !Filesystem::has(PATH['cache']) and Filesystem::createDir(PATH['cache']);

        // Set current time
        $this->now = time();

        // Create cache key to allow invalidate all cache on configuration changes.
        $this->key = ($this->flextype['registry']->get('settings.cache.prefix') ?? 'flextype') . '-' . md5(PATH['site'] . 'Flextype::VERSION');

        // Get Cache Driver
        $this->driver = $this->getCacheDriver();

        // Set the cache namespace to our unique key
        $this->driver->setNamespace($this->$key);
    }


    /**
     * Get Cache Driver
     *
     * @access public
     * @return object
     */
    public function getCacheDriver()
    {
        // Try to set default cache driver name
        $driver_name = $this->setDefaultCacheDriverName($this->flextype['registry']->get('settings.cache.driver'));

        // Set cache driver
        return $this->setCacheDriver($driver_name);
    }

    protected function setCacheDriver(string $driver_name)
    {
        switch ($driver_name) {
            case 'apcu':
               $driver = $this->setApcuCacheDriver();
            break;
            case 'array':
                $driver = $this->setArrayCacheDriver();
            break;
            case 'wincache':
               $driver = $this->setWinCacheDriver();
            break;
            case 'memcached':
                $driver = $this->setMemcachedCacheDriver();
            break;
            case 'sqlite3':
                $driver = $this->setSQLite3CacheDriver();
            break;
            case 'zend':
                $driver = $this->setZendDataCacheDriver();
            break;
            case 'redis':
                $driver = $this->setRedisCacheDriver();
            break;
            default:
                $driver = $this->setFilesystemCacheDriver();
            break;
        }

        return $driver;
    }

    /**
     * The ZendDataCache driver uses the Zend Data Cache API available in the Zend Platform.
     *
     * @access protected
     */
    protected function setZendDataCacheDriver()
    {
        $driver = new DoctrineCache\ZendDataCache();

        return $driver;
    }

    /**
     * The SQLite3Cache driver stores the cache data in a SQLite database and depends on the sqlite3 extension
     * http://php.net/manual/en/book.sqlite3.php
     *
     * @access protected
     */
    protected function setSQLite3CacheDriver()
    {
        // Cache directory
        $cache_directory = PATH['cache'] . '/doctrine/';

        // Create doctrine cache directory if its not exists
        !Filesystem::has($cache_directory) and Filesystem::createDir($cache_directory);

        $db = new \SQLite3($cache_directory . $this->flextype['registry']->get('settings.cache.sqlite3.database', 'flextype') . '.db');
        $driver = new DoctrineCache\SQLite3Cache($db, $this->flextype['registry']->get('settings.cache.sqlite3.table', 'flextype'));

        return $driver;
    }

    /**
     * The MemcachedCache drivers stores the cache data in Memcached.
     *
     * @access protected
     */
    protected function setMemcachedCacheDriver()
    {
        $memcached = new \Memcached();
        $memcached->addServer(
            $this->flextype['registry']->get('settings.cache.memcached.server', 'localhost'),
            $this->flextype['registry']->get('settings.cache.memcache.port', 11211)
        );
        $driver = new DoctrineCache\MemcachedCache();
        $driver->setMemcached($memcached);

        return $driver;
    }

    /**
     * The WinCacheCache driver uses the wincache_ucache_get, wincache_ucache_exists, etc. functions
     * that come with the wincache extension
     * http://php.net/manual/en/book.wincache.php
     *
     * @access protected
     */
    protected function setWinCacheDriver()
    {
        $driver = new DoctrineCache\WinCacheCache();

        return $driver;
    }

    /**
     * The ArrayCache driver stores the cache data in PHPs memory and is not persisted anywhere.
     * This can be useful for caching things in memory for a single process when you don't need the cache to be persistent across processes.
     * @access protected
     */
    protected function setArrayCacheDriver()
    {
        $driver = new DoctrineCache\ArrayCache();

        return $driver;
    }

    /**
     * The ApcuCache driver uses the apcu_fetch, apcu_exists, etc. functions
     * that come with PHP so no additional setup is required in order to use it.
     *
     * @access protected
     */
    protected function setApcuCacheDriver()
    {
        $driver = new DoctrineCache\ApcuCache();

        return $driver;
    }

    /**
     * The RedisCache driver stores the cache data in Redis and depends on the phpredis extension
     * https://github.com/phpredis/phpredis
     *
     * @access protected
     */
    protected function setRedisCacheDriver()
    {
        $redis    = new \Redis();
        $socket   = $this->flextype['registry']->get('settings.cache.redis.socket', false);
        $password = $this->flextype['registry']->get('settings.cache.redis.password', false);

        if ($socket) {
            $redis->connect($socket);
        } else {
            $redis->connect(
                $this->flextype['registry']->get('settings.cache.redis.server', 'localhost'),
                $this->flextype['registry']->get('settings.cache.redis.port', 6379)
            );
        }

        // Authenticate with password if set
        if ($password && !$redis->auth($password)) {
            throw new \RedisException('Redis authentication failed');
        }

        $driver = new DoctrineCache\RedisCache();
        $driver->setRedis($redis);

        return $driver;
    }

    /**
     * Filesystem cache Driver
     *
     * @access protected
     */
    protected function setFilesystemCacheDriver()
    {
        // Cache directory
        $cache_directory = PATH['cache'] . '/doctrine/';

        // Create doctrine cache directory if its not exists
        !Filesystem::has($cache_directory) and Filesystem::createDir($cache_directory);
        $driver = new DoctrineCache\FilesystemCache($cache_directory);

        return $driver;
    }

    /**
     * Set Default Cache Driver Name
     *
     * @access protected
     * @param string $driver_name Driver name.
     * @return string
     */
    protected function setDefaultCacheDriverName(string $driver_name)
    {
        if (!$driver_name || $driver_name == 'auto') {
            if (extension_loaded('apcu')) {
                $driver_name = 'apcu';
            } elseif (extension_loaded('wincache')) {
                $driver_name = 'wincache';
            } else {
                $driver_name = 'file';
            }
        }

        return $driver_name;
    }

    /**
     * Returns driver variable
     *
     * @access public
     * @return object
     */
    public function driver()
    {
        return $this->$driver;
    }

    /**
     * Get cache key.
     *
     * @access public
     * @return string
     */
    public function getKey() : string
    {
        return $this->$key;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @access public
     * @param string $id The id of the cache entry to fetch.
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch(string $id)
    {
        if ($this->flextype['registry']->get('settings.cache.enabled')) {
            return $this->$driver->fetch($id);
        } else {
            return false;
        }
    }

    /**
     * Returns a boolean state of whether or not the item exists in the cache based on id key
     *
     * @param string $id    the id of the cached data entry
     * @return bool         true if the cached items exists
     */
    public function contains($id)
    {
        if ($this->flextype['registry']->get('settings.cache.enabled')) {
            return $this->$driver->contains(($id));
        } else {
            return false;
        }
    }

    /**
     * Puts data into the cache.
     *
     * @access public
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifetime The lifetime in number of seconds for this cache entry.
     *                         If zero (the default), the entry never expires (although it may be deleted from the cache
     *                         to make place for other entries).
     */
    public function save(string $id, $data, $lifetime = null)
    {
        if ($this->flextype['registry']->get('settings.cache.enabled')) {
            if ($lifetime === null) {
                $lifetime = $this->getLifetime();
            }
            $this->$driver->save($id, $data, $lifetime);
        }
    }

    /**
     * Clear Cache
     */
    public function clear() : void
    {
        // Clear stat cache
        @clearstatcache();

        // Clear opcache
        function_exists('opcache_reset') and @opcache_reset();

        // Remove cache dirs
        Filesystem::deleteDir(PATH['cache'] . '/doctrine/');
        Filesystem::deleteDir(PATH['cache'] . '/glide/');
    }

    /**
     * Set the cache lifetime.
     *
     * @access public
     * @param int $future timestamp
     */
    public function setLifetime(int $future)
    {
        if (!$future) {
            return;
        }

        $interval = $future-$this->$now;

        if ($interval > 0 && $interval < $this->getLifetime()) {
            $this->$lifetime = $interval;
        }
    }

    /**
     * Retrieve the cache lifetime (in seconds)
     *
     * @access public
     * @return mixed
     */
    public function getLifetime()
    {
        if ($this->$lifetime === null) {
            $this->$lifetime = $this->flextype['registry']->get('settings.cache.lifetime') ?: 604800;
        }

        return $this->$lifetime;
    }
}
