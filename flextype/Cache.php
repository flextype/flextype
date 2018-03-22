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
     * Constructor.
     *
     * @access  protected
     */
    protected function __construct()
    {
        // Set current time
        static::$now = time();

        // Cache key allows us to invalidate all cache on configuration changes.
        static::$key = (Config::get('site.cache.prefix') ? Config::get('site.cache.prefix') : 'flextype') . '-' . md5(ROOT_DIR . Flextype::VERSION);

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
        $driver_name = Config::get('site.cache.driver');
        if (!$driver_name || $driver_name == 'auto') {
            if (extension_loaded('apc')) {
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
                $driver = new \Doctrine\Common\Cache\ApcCache();
                break;
            case 'wincache':
                $driver = new \Doctrine\Common\Cache\WinCacheCache();
                break;
            case 'xcache':
                $driver = new \Doctrine\Common\Cache\XcacheCache();
                break;
            case 'memcache':
                $memcache = new \Memcache();
                $memcache->connect(Config::get('site.cache.memcache.server', 'localhost'),
                                   Config::get('site.cache.memcache.port', 11211));
                $driver = new \Doctrine\Common\Cache\MemcacheCache();
                $driver->setMemcache($memcache);
                break;
            case 'redis':
                $redis = new \Redis();
                $redis->connect(Config::get('site.cache.redis.server', 'localhost'),
                                Config::get('site.cache.redis.port', 6379));
                $driver = new \Doctrine\Common\Cache\RedisCache();
                $driver->setRedis($redis);
                break;
            default:
                // Create doctrine cache directory if its not exists
                !Flextype::filesystem()->exists($cache_directory = CACHE_PATH . '/doctrine/') and Flextype::filesystem()->mkdir($cache_directory);
                $driver = new \Doctrine\Common\Cache\FilesystemCache($cache_directory);
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
    public static function getKey()
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
    public function fetch($id)
    {
        if (Config::get('site.cache.enabled')) {
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
    public function save($id, $data, $lifetime = null)
    {
        if (Config::get('site.cache.enabled')) {
            if ($lifetime === null) {
                $lifetime = static::getLifetime();
            }
            static::$driver->save($id, $data, $lifetime);
        }
    }

    /**
     * Clear Cache
     */
    public static function clear()
    {
        Flextype::filesystem()->remove(CACHE_PATH . '/doctrine/');
    }

    /**
     * Set the cache lifetime.
     *
     * @access public
     * @param int $future timestamp
     */
    public static function setLifetime($future)
    {
        if (!$future) {
            return;
        }
        $interval = $future - $this->now;
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
            static::$lifetime = Config::get('site.cache.lifetime') ?: 604800;
        }
        return static::$lifetime;
    }

    /**
     * Initialize Flextype Cache
     *
     * @access public
     * @return object
     */
    public static function init()
    {
        return !isset(self::$instance) and self::$instance = new Cache();
    }
}
