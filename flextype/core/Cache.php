<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Doctrine\Common\Cache as DoctrineCache;
use Flextype\Component\Filesystem\Filesystem;
use Memcached;
use Redis;
use RedisException;
use SQLite3;
use function clearstatcache;
use function extension_loaded;
use function function_exists;
use function md5;
use function opcache_reset;
use function time;

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
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;

        // Create Cache Directory
        ! Filesystem::has(PATH['cache']) and Filesystem::createDir(PATH['cache']);

        // Set current time
        $this->now = time();

        // Create cache key to allow invalidate all cache on configuration changes.
        $this->key = ($this->flextype['registry']->get('settings.cache.prefix') ?? 'flextype') . '-' . md5(PATH['site'] . 'Flextype::VERSION');

        // Get Cache Driver
        $this->driver = $this->getCacheDriver();

        // Set the cache namespace to our unique key
        $this->driver->setNamespace($this->key);
    }

    /**
     * Get Cache Driver
     *
     * @access public
     */
    public function getCacheDriver() : object
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
        return new DoctrineCache\ZendDataCache();
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
        ! Filesystem::has($cache_directory) and Filesystem::createDir($cache_directory);

        $db = new SQLite3($cache_directory . $this->flextype['registry']->get('settings.cache.sqlite3.database', 'flextype') . '.db');

        return new DoctrineCache\SQLite3Cache($db, $this->flextype['registry']->get('settings.cache.sqlite3.table', 'flextype'));
    }

    /**
     * The MemcachedCache drivers stores the cache data in Memcached.
     *
     * @access protected
     */
    protected function setMemcachedCacheDriver()
    {
        $memcached = new Memcached();
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
        return new DoctrineCache\WinCacheCache();
    }

    /**
     * The ArrayCache driver stores the cache data in PHPs memory and is not persisted anywhere.
     * This can be useful for caching things in memory for a single process when you don't need the cache to be persistent across processes.
     *
     * @access protected
     */
    protected function setArrayCacheDriver()
    {
        return new DoctrineCache\ArrayCache();
    }

    /**
     * The ApcuCache driver uses the apcu_fetch, apcu_exists, etc. functions
     * that come with PHP so no additional setup is required in order to use it.
     *
     * @access protected
     */
    protected function setApcuCacheDriver()
    {
        return new DoctrineCache\ApcuCache();
    }

    /**
     * The RedisCache driver stores the cache data in Redis and depends on the phpredis extension
     * https://github.com/phpredis/phpredis
     *
     * @access protected
     */
    protected function setRedisCacheDriver()
    {
        $redis    = new Redis();
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
        if ($password && ! $redis->auth($password)) {
            throw new RedisException('Redis authentication failed');
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
        ! Filesystem::has($cache_directory) and Filesystem::createDir($cache_directory);

        return new DoctrineCache\FilesystemCache($cache_directory);
    }

    /**
     * Set Default Cache Driver Name
     *
     * @param string $driver_name Driver name.
     *
     * @access protected
     */
    protected function setDefaultCacheDriverName(string $driver_name) : string
    {
        if (! $driver_name || $driver_name === 'auto') {
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
     */
    public function driver() : object
    {
        return $this->driver;
    }

    /**
     * Get cache key.
     *
     * @access public
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     *
     * @access public
     */
    public function fetch(string $id)
    {
        if ($this->flextype['registry']->get('settings.cache.enabled')) {
            return $this->driver->fetch($id);
        }

        return false;
    }

    /**
     * Returns a boolean state of whether or not the item exists in the cache based on id key
     *
     * @param string $id the id of the cached data entry
     *
     * @return bool         true if the cached items exists
     */
    public function contains(string $id) : bool
    {
        if ($this->flextype['registry']->get('settings.cache.enabled')) {
            return $this->driver->contains($id);
        }

        return false;
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifetime The lifetime in number of seconds for this cache entry.
     *                         If zero (the default), the entry never expires (although it may be deleted from the cache
     *                         to make place for other entries).
     *
     * @access public
     */
    public function save(string $id, $data, ?int $lifetime = null) : void
    {
        if (! $this->flextype['registry']->get('settings.cache.enabled')) {
            return;
        }

        if ($lifetime === null) {
            $lifetime = $this->getLifetime();
        }
        $this->driver->save($id, $data, $lifetime);
    }

    /**
     * Delete item from the chache
     */
    public function delete(string $id) : void
    {
        if (! $this->flextype['registry']->get('settings.cache.enabled')) {
            return;
        }

        $this->driver->delete($id);
    }

    /**
     * Clear Cache
     */
    public function clear(string $id) : void
    {
        // Clear stat cache
        @clearstatcache();

        // Clear opcache
        function_exists('opcache_reset') and @opcache_reset();

        // Remove cache dirs
        Filesystem::deleteDir(PATH['cache'] . '/' . $id);
    }

    /**
     * Clear ALL Cache
     */
    public function clearAll() : void
    {
        // Clear stat cache
        @clearstatcache();

        // Clear opcache
        function_exists('opcache_reset') and @opcache_reset();

        // Remove cache directory
        Filesystem::deleteDir(PATH['cache']);
    }

    /**
     * Set the cache lifetime.
     *
     * @param int $future timestamp
     *
     * @access public
     */
    public function setLifetime(int $future) : void
    {
        if (! $future) {
            return;
        }

        $interval = $future-$this->now;

        if ($interval <= 0 || $interval >= $this->getLifetime()) {
            return;
        }

        $this->lifetime = $interval;
    }

    /**
     * Retrieve the cache lifetime (in seconds)
     *
     * @return mixed
     *
     * @access public
     */
    public function getLifetime()
    {
        if ($this->lifetime === null) {
            $this->lifetime = $this->flextype['registry']->get('settings.cache.lifetime') ?: 604800;
        }

        return $this->lifetime;
    }
}
