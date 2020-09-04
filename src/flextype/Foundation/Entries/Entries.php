<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation\Entries;

use Flextype\Component\Filesystem\Filesystem;
use function array_merge;
use function collect_filter;
use function count;
use function find_filter;
use function ltrim;
use function md5;
use function rename;
use function rtrim;
use function str_replace;

class Entries
{
    /**
     * Entries Storage
     *
     * Used for storing current requested entry(entries) data
     * and maybe changed on fly.
     *
     * @var array
     * @access public
     */
    public $storage = [];

    /**
     * Fetch entry(entries)
     *
     * @param string $id         Unique identifier of the entry(entries).
     * @param bool   $collection Set `true` if collection of entries need to be fetched.
     * @param array  $filter     Select items in collection by given conditions.
     *
     * @return array|bool|int
     *
     * @access public
     */
    public function fetch(string $id, bool $collection = false, array $filter = [])
    {
        if ($collection) {
            return $this->fetchCollection($id, $filter);
        }

        return $this->fetchSingle($id);
    }

    /**
     * Fetch single entry
     *
     * @param string $id Unique identifier of the entry(entries).
     *
     * @return array The entry array data.
     *
     * @access public
     */
    public function fetchSingle(string $id) : array
    {
        // Store data
        $this->storage['fetch_single']['id'] = $id;

        // Run event: onEntryInitialized
        flextype('emitter')->emit('onEntryInitialized');

        // Get Cache ID for current requested entry
        $entry_cache_id = $this->getCacheID($this->storage['fetch_single']['id']);

        // Try to get current requested entry from cache
        if (flextype('cache')->has($entry_cache_id)) {
            // Fetch entry from cache
            $this->storage['fetch_single']['data'] = flextype('cache')->get($entry_cache_id);

            // Run event: onEntryAfterCacheInitialized
            flextype('emitter')->emit('onEntryAfterCacheInitialized');

            // Return entry from cache
            return $this->storage['fetch_single']['data'];
        }

        // Try to get current requested entry from filesystem
        if ($this->has($this->storage['fetch_single']['id'])) {
            // Get entry file location
            $entry_file = $this->getFileLocation($this->storage['fetch_single']['id']);

            // Try to get requested entry from the filesystem
            $entry_file_content = Filesystem::read($entry_file);
            if ($entry_file_content === false) {
                return [];
            }

            // Decode entry file content
            $this->storage['fetch_single']['data'] = flextype('frontmatter')->decode($entry_file_content);

            // Run event: onEntryAfterInitialized
            flextype('emitter')->emit('onEntryAfterInitialized');

            // Set cache state
            $cache = flextype('entries')->storage['fetch_single']['data']['cache']['enabled'] ??
                                flextype('registry')->get('flextype.settings.cache.enabled');

            // Save entry data to cache
            if ($cache) {
                flextype('cache')->set($entry_cache_id, $this->storage['fetch_single']['data']);
            }

            // Return entry data
            return $this->storage['fetch_single']['data'];
        }

        // Return empty array if entry is not founded
        return [];
    }

    /**
     * Fetch entries collection
     *
     * @param string $id     Unique identifier of the entry(entries).
     * @param array  $filter Select items in collection by given conditions.
     *
     * @return array|bool|int
     *
     * @access public
     */
    public function fetchCollection(string $id, array $filter = [])
    {
        // Store data
        $this->storage['fetch_collection']['id'] = $this->getDirLocation($id);
        $this->storage['fetch_collection']['data'] = [];

        // Run event: onEntriesInitialized
        flextype('emitter')->emit('onEntriesInitialized');

        // Find entries
        $entries_list = find_filter($this->storage['fetch_collection']['id'], $filter);

        // If entries founded in the entries folder
        // We are checking... Whether the requested entry is an a true entry.
        // Get entry $_id. Remove entries path and remove left and right slashes.
        // Fetch single entry.
        if (count($entries_list) > 0) {
            foreach ($entries_list as $current_entry) {
                if ($current_entry->getType() !== 'file' || $current_entry->getFilename() !== 'entry' . '.' . flextype('registry')->get('flextype.settings.entries.extension')) {
                    continue;
                }

                $_id                 = ltrim(rtrim(str_replace(PATH['project'] . '/entries/', '', str_replace('\\', '/', $current_entry->getPath())), '/'), '/');
                $this->storage['fetch_collection']['data'][$_id] = $this->fetchSingle($_id);
            }

            // Apply collection filter
            $this->storage['fetch_collection']['data'] = collect_filter($this->storage['fetch_collection']['data'], $filter);

            // Run event: onEntriesAfterInitialized
            flextype('emitter')->emit('onEntriesAfterInitialized');
        }

        // Return entries array
        return $this->storage['fetch_collection']['data'];
    }

    /**
     * Rename entry
     *
     * @param string $id     Unique identifier of the entry(entries).
     * @param string $new_id New Unique identifier of the entry(entries).
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function rename(string $id, string $new_id) : bool
    {
        // Store data
        $this->storage['rename']['id'] = $id;
        $this->storage['rename']['new_id'] = $new_id;

        // Run event: onEntryRename
        flextype('emitter')->emit('onEntryRename');

        if (! $this->has($this->storage['rename']['new_id'])) {
            return rename($this->getDirLocation($this->storage['rename']['id']), $this->getDirLocation($this->storage['rename']['new_id']));
        }

        return false;
    }

    /**
     * Update entry
     *
     * @param string $id   Unique identifier of the entry(entries).
     * @param array  $data Data to update for the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function update(string $id, array $data) : bool
    {
        // Store data
        $this->storage['update']['id'] = $id;
        $this->storage['update']['data'] = $data;

        // Run event: onEntryUpdate
        flextype('emitter')->emit('onEntryUpdate');

        $entry_file = $this->getFileLocation($this->storage['update']['id']);

        if (Filesystem::has($entry_file)) {
            $body  = Filesystem::read($entry_file);
            $entry = flextype('frontmatter')->decode($body);

            return Filesystem::write($entry_file, flextype('frontmatter')->encode(array_merge($entry, $this->storage['update']['data'])));
        }

        return false;
    }

    /**
     * Create entry
     *
     * @param string $id   Unique identifier of the entry(entries).
     * @param array  $data Data to create for the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function create(string $id, array $data) : bool
    {
        // Store data
        $this->storage['create']['id'] = $id;
        $this->storage['create']['data'] = $data;

        // Run event: onEntryCreate
        flextype('emitter')->emit('onEntryCreate');

        $entry_dir = $this->getDirLocation($this->storage['create']['id']);

        if (! Filesystem::has($entry_dir)) {
            // Try to create directory for new entry
            if (Filesystem::createDir($entry_dir)) {
                // Check if new entry file exists
                if (! Filesystem::has($entry_file = $entry_dir . '/entry' . '.' . flextype('registry')->get('flextype.settings.entries.extension'))) {

                    // Create a new entry!
                    return Filesystem::write($entry_file, flextype('frontmatter')->encode($this->storage['create']['data']));
                }

                return false;
            }
        }

        return false;
    }

    /**
     * Delete entry
     *
     * @param string $id Unique identifier of the entry(entries).
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id) : bool
    {
        // Store data
        $this->storage['delete']['id'] = $id;

        // Run event: onEntryDelete
        flextype('emitter')->emit('onEntryDelete');

        return Filesystem::deleteDir($this->getDirLocation($this->storage['delete']['id']));
    }

    /**
     * Copy entry(s)
     *
     * @param string $id     Unique identifier of the entry(entries).
     * @param string $new_id New Unique identifier of the entry(entries).
     * @param bool   $deep   Recursive copy entries.
     *
     * @return bool|null True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $new_id, bool $deep = false) : ?bool
    {
        // Store data
        $this->storage['copy']['id'] = $id;
        $this->storage['copy']['new_id'] = $new_id;
        $this->storage['copy']['deep'] = $deep;

        // Run event: onEntryRename
        flextype('emitter')->emit('onEntryCopy');

        return Filesystem::copy($this->getDirLocation($this->storage['copy']['id']), $this->getDirLocation($this->storage['copy']['new_id']), $this->storage['copy']['deep']);
    }

    /**
     * Check whether entry exists
     *
     * @param string $id Unique identifier of the entry(entries).
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function has(string $id) : bool
    {
        // Store data
        $this->storage['has']['id'] = $id;

        // Run event: onEntryHas
        flextype('emitter')->emit('onEntryHas');

        return Filesystem::has($this->getFileLocation($this->storage['has']['id']));
    }

    /**
     * Get entry file location
     *
     * @param string $id Unique identifier of the entry(entries).
     *
     * @return string entry file location
     *
     * @access public
     */
    public function getFileLocation(string $id) : string
    {
        return PATH['project'] . '/entries/' . $id . '/entry' . '.' . flextype('registry')->get('flextype.settings.entries.extension');
    }

    /**
     * Get entry directory location
     *
     * @param string $id Unique identifier of the entry(entries).
     *
     * @return string entry directory location
     *
     * @access public
     */
    public function getDirLocation(string $id) : string
    {
        return PATH['project'] . '/entries/' . $id;
    }

    /**
     * Get Cache ID for entry
     *
     * @param  string $id Unique identifier of the entry(entries).
     *
     * @return string Cache ID
     *
     * @access public
     */
    public function getCacheID(string $id) : string
    {
        if (flextype('registry')->get('flextype.settings.cache.enabled') === false) {
            return '';
        }

        $entry_file = $this->getFileLocation($id);

        if (Filesystem::has($entry_file)) {
            return md5('entry' . $entry_file . (Filesystem::getTimestamp($entry_file) ?: ''));
        }

        return md5('entry' . $entry_file);
    }
}
