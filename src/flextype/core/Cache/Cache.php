<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use function clearstatcache;
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
        $this->key = ($this->flextype['registry']->get('flextype.cache.prefix') ?? 'flextype') . '-' . md5(PATH['site'] . 'Flextype::VERSION');

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
        return $this->flextype['cache_adapter']->getDriver();
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
        if ($this->flextype['registry']->get('flextype.cache.enabled')) {
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
        if ($this->flextype['registry']->get('flextype.cache.enabled')) {
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
        if (! $this->flextype['registry']->get('flextype.cache.enabled')) {
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
        if (! $this->flextype['registry']->get('flextype.cache.enabled')) {
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
            $this->lifetime = $this->flextype['registry']->get('flextype.cache.lifetime') ?: 604800;
        }

        return $this->lifetime;
    }
}
