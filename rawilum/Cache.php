<?php namespace Rawilum;

/**
 * @package Rawilum
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://rawilum.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Cache
{
    /**
     * @var Rawilum
     */
    protected $rawilum;

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
     * Constructor.
     *
     * @access  protected
     */
    public function __construct(Rawilum $c)
    {
        $this->rawilum = $c;

        // Set current time
        static::$now = time();

        // Cache key allows us to invalidate all cache on configuration changes.
        static::$key = ($this->rawilum['config']->get('site.cache.prefix') ? $this->rawilum['config']->get('site.cache.prefix') : 'rawilum') . '-' . md5(ROOT_DIR . 'Rawilum::VERSION');

        // Get Cache Driver
        static::$driver = $this->getCacheDriver();

        // Set the cache namespace to our unique key
        static::$driver->setNamespace(static::$key);

        // Return
        return static::$driver;
    }

    /**
     * Get Cache Driver
     *
     * @access public
     * @return object
     */
    public function getCacheDriver()
    {
        $driver_name = $this->rawilum['config']->get('site.cache.driver');

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
                $memcache->connect(
                    $this->rawilum['config']->get('site.cache.memcache.server', 'localhost'),
                                   $this->rawilum['config']->get('site.cache.memcache.port', 11211)
                );
                $driver = new \Doctrine\Common\Cache\MemcacheCache();
                $driver->setMemcache($memcache);
                break;
            case 'redis':
                $redis = new \Redis();
                $redis->connect(
                    $this->rawilum['config']->get('site.cache.redis.server', 'localhost'),
                                $this->rawilum['config']->get('site.cache.redis.port', 6379)
                );
                $driver = new \Doctrine\Common\Cache\RedisCache();
                $driver->setRedis($redis);
                break;
            default:
                // Create doctrine cache directory if its not exists
                !$this->rawilum['filesystem']->exists($cache_directory = CACHE_PATH . '/doctrine/') and $this->rawilum['filesystem']->mkdir($cache_directory);
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
    public function driver()
    {
        return static::$driver;
    }

    /**
     * Get cache key.
     *
     * @access public
     * @return string
     */
    public function getKey()
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
        if ($this->rawilum['config']->get('site.cache.enabled')) {
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
        if ($this->rawilum['config']->get('site.cache.enabled')) {
            if ($lifetime === null) {
                $lifetime = static::getLifetime();
            }
            static::$driver->save($id, $data, $lifetime);
        }
    }

    /**
     * Clear Cache
     */
    public function clear()
    {
        $this->rawilum['filesystem']->remove(CACHE_PATH . '/doctrine/');
    }

    /**
     * Set the cache lifetime.
     *
     * @access public
     * @param int $future timestamp
     */
    public function setLifetime($future)
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
    public function getLifetime()
    {
        if (static::$lifetime === null) {
            static::$lifetime = $this->rawilum['config']->get('site.cache.lifetime') ?: 604800;
        }
        return static::$lifetime;
    }
}
