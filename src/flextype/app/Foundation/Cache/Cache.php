<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation\Cache;

use Flextype\Component\Filesystem\Filesystem;
use function clearstatcache;
use function error_reporting;
use function function_exists;
use function md5;
use function opcache_reset;
use function time;

class Cache
{
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
     * @var object
     */
    private $driver;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {


        // Create Cache Directory
        ! Filesystem::has(PATH['cache']) and Filesystem::createDir(PATH['cache']);

        // Set current time
        $this->now = time();

        // Create cache key to allow invalidate all cache on configuration changes.
        $cache_prefix        = (flextype('registry')->get('flextype.settings.cache.prefix') ?? 'flextype');
        $cache_unique_string = md5(PATH['project'] . flextype('registry')->get('flextype.manifest.version'));
        $this->key           = $cache_prefix . $cache_unique_string;

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
        return flextype('cache_adapter')->getDriver();
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
     * @return mixed|false The cached data or FALSE, if no cache entry exists for the given id.
     *
     * @access public
     */
    public function fetch(string $id)
    {
        if (flextype('registry')->get('flextype.settings.cache.enabled')) {
            return $this->driver->fetch($id);
        }

        return false;
    }

    /**
     * Fetches multiplay items from the cache.
     *
     * @param array $keys Array of keys to retrieve from cache
     *
     * @return array Array of values retrieved for the given keys.
     */
    public function fetchMultiple(array $keys) : array
    {
        if (flextype('registry')->get('flextype.settings.cache.enabled')) {
            return $this->driver->fetchMultiple($keys);
        }

        return [];
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
        if (flextype('registry')->get('flextype.settings.cache.enabled')) {
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
     *                         to make place for other items).
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't.
     *
     * @access public
     */
    public function save(string $id, $data, ?int $lifetime = null) : bool
    {
        if (! flextype('registry')->get('flextype.settings.cache.enabled')) {
            return false;
        }

        if ($lifetime === null) {
            $lifetime = $this->getLifetime();
        }

        return $this->driver->save($id, $data, $lifetime);
    }

    /**
     * Puts multiple data into the cache.
     *
     * @param array $keysAndValues Array of keys and values to save in cache
     * @param int   $lifetime      The lifetime. If != 0, sets a specific lifetime for these
     *                             cache items (0 => infinite lifeTime).
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't.
     */
    public function saveMultiple(array $keysAndValues, int $lifetime = 0) : bool
    {
        if (! flextype('registry')->get('flextype.settings.cache.enabled')) {
            return false;
        }

        return $this->saveMultiple($keysAndValues, $lifetime);
    }

    /**
     * Delete item from the cache
     *
     * @param string $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    public function delete(string $id) : bool
    {
        if (! flextype('registry')->get('flextype.settings.cache.enabled')) {
            return false;
        }

        $this->driver->delete($id);
    }

    /**
     * Delete multiple item from the cache.
     *
     * @param array $keys Array of keys to delete from cache
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't
     */
    public function deleteMultiple(array $keys) : bool
    {
        if (! flextype('registry')->get('flextype.settings.cache.enabled')) {
            return false;
        }

        return $this->driver->deleteMultiple($keys);
    }

    /**
     * Retrieves cached information from the data store.
     *
     * @return array|null An associative array with server's statistics if available, NULL otherwise.
     */
    public function getStats() : ?array
    {
        if (! flextype('registry')->get('flextype.settings.cache.enabled')) {
            return false;
        }

        return $this->driver->getStats();
    }

    /**
     * Deletes all cache
     */
    public function deleteAll() : bool
    {
        if (! flextype('registry')->get('flextype.settings.cache.enabled')) {
            return false;
        }

        return $this->driver->deleteAll();
    }

    /**
     * Flushes all cache items.
     *
     * @return bool TRUE if the cache items were successfully flushed, FALSE otherwise.
     */
    public function flushAll() : bool
    {
        if (! flextype('registry')->get('flextype.settings.cache.enabled')) {
            return false;
        }

        return $this->driver->flushAll();
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
            $this->lifetime = flextype('registry')->get('flextype.settings.cache.lifetime') ?: 604800;
        }

        return $this->lifetime;
    }

    /**
     * Purge specific cache directory
     *
     * @param $directory Directory to purge
     *
     * @access public
     */
    public function purge(string $directory) : void
    {
        // Run event: onCacheBeforePurge
        flextype('emitter')->emit('onCacheBeforePurge');

        // Remove specific cache directory
        Filesystem::deleteDir(PATH['cache'] . '/' . $directory);

        // Save and Mute error_reporting
        $errorReporting = error_reporting();
        error_reporting(0);

        // Clear stat cache
        clearstatcache();

        // Clear opcache
        function_exists('opcache_reset') and opcache_reset();

        // Restore error_reporting
        error_reporting($errorReporting);

        // Run event: onCacheAfterPurge
        flextype('emitter')->emit('onCacheAfterPurge');
    }

    /**
     * Purge ALL Cache directories
     *
     * @access public
     */
    public function purgeAll() : void
    {
        // Run event: onCacheAfterPurgeAll
        flextype('emitter')->emit('onCacheAfterPurgeAll');

        // Remove cache directory
        Filesystem::deleteDir(PATH['cache']);

        // Save and Mute error_reporting
        $errorReporting = error_reporting();
        error_reporting(0);

        // Clear stat cache
        clearstatcache();

        // Clear opcache
        function_exists('opcache_reset') and opcache_reset();

        // Restore error_reporting
        error_reporting($errorReporting);

        // Run event: onCacheAfterPurgeAll
        flextype('emitter')->emit('onCacheAfterPurgeAll');
    }
}
