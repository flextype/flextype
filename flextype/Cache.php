<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\{Filesystem\Filesystem, Registry\Registry};
use \Doctrine\Common\Cache as DoctrineCache;

class Cache
{
    /**
     * An instance of the Cache class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Unique cache key
     *
     * @var string Cache key.
     */
    protected static $key;

    /**
     * Lifetime
     *
     * @var int Lifetime.
     */
    protected static $lifetime;

    /**
     * Current time
     *
     * @var int Current time.
     */
    protected static $now;

    /**
     * Cache Driver
     *
     * @var DoctrineCache
     */
    protected static $driver;

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access  protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        static::init();
    }

    /**
     * Init Cache
     *
     * @access protected
     * @return void
     */
    protected static function init() : void
    {
        // Set current time
        static::$now = time();

        // Create cache key to allow invalidate all cache on configuration changes.
        static::$key = (Registry::get('site.cache.prefix') ?? 'flextype') . '-' . md5(ROOT_DIR . Flextype::VERSION);

        // Get Cache Driver
        static::$driver = static::getCacheDriver();

        // Set the cache namespace to our unique key
        static::$driver->setNamespace(static::$key);
    }

    /**
     * Get Cache Driver
     *
     * @access public
     * @return object
     */
    public static function getCacheDriver()
    {
        $driver_name = Registry::get('site.cache.driver');

        if (!$driver_name || $driver_name == 'auto') {
            if (extension_loaded('apcu')) {
                $driver_name = 'apcu';
            } elseif (extension_loaded('apc')) {
                $driver_name = 'apc';
            } elseif (extension_loaded('wincache')) {
                $driver_name = 'wincache';
            } elseif (extension_loaded('xcache')) {
                $driver_name = 'xcache';
            }
        } else {
            $driver_name = 'file';
        }

        switch ($driver_name) {
            case 'apc':
               $driver = new DoctrineCache\ApcCache();
               break;
            case 'apcu':
               $driver = new DoctrineCache\ApcuCache();
               break;
            case 'wincache':
               $driver = new DoctrineCache\WinCacheCache();
               break;
            case 'xcache':
               $driver = new DoctrineCache\XcacheCache();
               break;
            case 'memcache':
                $memcache = new \Memcache();
                $memcache->connect(Registry::get('site.cache.memcache.server', 'localhost'),
                                   Registry::get('site.cache.memcache.port', 11211));
                $driver = new DoctrineCache\MemcacheCache();
                $driver->setMemcache($memcache);
                break;
            case 'memcached':
                $memcached = new \Memcached();
                $memcached->addServer(Registry::get('site.cache.memcached.server', 'localhost'),
                                      Registry::get('site.cache.memcache.port', 11211));
                $driver = new DoctrineCache\MemcachedCache();
                $driver->setMemcached($memcached);
                break;
            case 'redis':
                $redis    = new \Redis();
                $socket   = Registry::get('site.cache.redis.socket', false);
                $password = Registry::get('site.cache.redis.password', false);

                if ($socket) {
                    $redis->connect($socket);
                } else {
                    $redis->connect(Registry::get('site.cache.redis.server', 'localhost'),
                                    Registry::get('site.cache.redis.port', 6379));
                }

                // Authenticate with password if set
                if ($password && !$redis->auth($password)) {
                    throw new \RedisException('Redis authentication failed');
                }

                $driver = new DoctrineCache\RedisCache();
                $driver->setRedis($redis);
                break;
            default:
                // Create doctrine cache directory if its not exists
                !Filesystem::fileExists($cache_directory = CACHE_PATH . '/doctrine/') and Filesystem::createDir($cache_directory);
                $driver = new DoctrineCache\FilesystemCache($cache_directory);
                break;
        }
        return $driver;
    }

    /**
     * Returns driver variable
     *
     * @access public
     * @return object
     */
    public static function driver()
    {
        return static::$driver;
    }

    /**
     * Get cache key.
     *
     * @access public
     * @return string
     */
    public static function getKey() : string
    {
        return static::$key;
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
        if (Registry::get('site.cache.enabled')) {
            return static::$driver->fetch($id);
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
     * @param int    $lifeTime The lifetime in number of seconds for this cache entry.
     *                         If zero (the default), the entry never expires (although it may be deleted from the cache
     *                         to make place for other entries).
     */
    public function save(string $id, $data, $lifetime = null)
    {
        if (Registry::get('site.cache.enabled')) {
            if ($lifetime === null) {
                $lifetime = static::getLifetime();
            }
            static::$driver->save($id, $data, $lifetime);
        }
    }

    /**
     * Clear Cache
     */
    public static function clear() : void
    {
        // Clear stat cache
        @clearstatcache();

        // Clear opcache
        function_exists('opcache_reset') and @opcache_reset();

        // Remove cache dir
        Filesystem::deleteDir(CACHE_PATH . '/doctrine/');
    }

    /**
     * Set the cache lifetime.
     *
     * @access public
     * @param int $future timestamp
     */
    public static function setLifetime(int $future)
    {
        if (!$future) {
            return;
        }

        $interval = $future - static::$now;

        if ($interval > 0 && $interval < static::getLifetime()) {
            static::$lifetime = $interval;
        }
    }

    /**
     * Retrieve the cache lifetime (in seconds)
     *
     * @access public
     * @return mixed
     */
    public static function getLifetime()
    {
        if (static::$lifetime === null) {
            static::$lifetime = Registry::get('site.cache.lifetime') ?: 604800;
        }

        return static::$lifetime;
    }

    /**
     * Return the Cache instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Cache();
    }
}
