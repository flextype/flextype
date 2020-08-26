<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation\Entries;

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
     * Current entry id
     *
     * @var string
     * @access public
     */
    public $entry_id = null;

    /**
     * Current entry data array
     *
     * @var array
     * @access public
     */
    public $entry = [];

    /**
     * Current entry create data array
     *
     * @var array
     * @access public
     */
    public $entry_create_data = [];

    /**
     * Current entry update data array
     *
     * @var array
     * @access public
     */
    public $entry_update_data = [];

    /**
     * Current entries data
     *
     * @var array|bool|int
     * @access public
     */
    public $entries;

    /**
     * Current entries id
     *
     * @var string
     * @access public
     */
    public $entries_id = null;

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
        // Store current requested entry id
        $this->entry_id = $id;

        // Get Cache ID for current requested entry
        $entry_cache_id = $this->getCacheID($this->entry_id);

        // Try to get current requested entry from cache
        if (flextype('cache')->contains($entry_cache_id)) {
            // Fetch entry from cache
            $this->entry = flextype('cache')->fetch($entry_cache_id);

            // Run event: onEntryAfterCacheInitialized
            flextype('emitter')->emit('onEntryAfterCacheInitialized');

            // Return entry from cache
            return $this->entry;
        }

        // Try to get current requested entry from filesystem
        if ($this->has($this->entry_id)) {
            // Get entry file location
            $entry_file = $this->getFileLocation($this->entry_id);

            // Try to get requested entry from the filesystem
            $entry_file_content = Filesystem::read($entry_file);
            if ($entry_file_content === false) {
                return [];
            }

            // Decode entry file content
            $this->entry = flextype('frontmatter')->decode($entry_file_content);

            // Run event: onEntryAfterInitialized
            flextype('emitter')->emit('onEntryAfterInitialized');

            // Set cache state
            $cache = flextype('entries')->entry['cache']['enabled'] ??
                                flextype('registry')->get('flextype.settings.cache.enabled');

            // Save entry data to cache
            if ($cache) {
                flextype('cache')->save($entry_cache_id, $this->entry);
            }

            // Return entry data
            return $this->entry;
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
        // Init Entries object
        $this->entries = [];

        // Store current requested entries id
        $this->entries_id = $this->getDirLocation($id);

        // Find entries
        $entries_list = find_filter($this->entries_id, $filter);

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
                $this->entries[$_id] = $this->fetchSingle($_id);
            }

            // Apply collection filter
            $this->entries = collect_filter($this->entries, $filter);

            // Run event: onEntriesAfterInitialized
            flextype('emitter')->emit('onEntriesAfterInitialized');
        }

        // Return entries array
        return $this->entries;
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
        if (! Filesystem::has($this->getDirLocation($new_id))) {
            // Run event: onEntryRename
            flextype('emitter')->emit('onEntryRename');

            return rename($this->getDirLocation($id), $this->getDirLocation($new_id));
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
        $entry_file = $this->getFileLocation($id);

        if (Filesystem::has($entry_file)) {
            $body  = Filesystem::read($entry_file);
            $entry = flextype('frontmatter')->decode($body);

            // Store data in the entry_update_data
            $this->entry_update_data = $data;

            // Run event: onEntryUpdate
            flextype('emitter')->emit('onEntryUpdate');

            return Filesystem::write($entry_file, flextype('frontmatter')->encode(array_merge($entry, $this->entry_update_data)));
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
        $entry_dir = $this->getDirLocation($id);

        if (! Filesystem::has($entry_dir)) {
            // Try to create directory for new entry
            if (Filesystem::createDir($entry_dir)) {
                // Check if new entry file exists
                if (! Filesystem::has($entry_file = $entry_dir . '/entry' . '.' . flextype('registry')->get('flextype.settings.entries.extension'))) {
                    // Store data in the entry_create_data
                    $this->entry_create_data = $data;

                    // Run event: onEntryCreate
                    flextype('emitter')->emit('onEntryCreate');

                    // Create a new entry!
                    return Filesystem::write($entry_file, flextype('frontmatter')->encode($this->entry_create_data));
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
        // Run event: onEntryDelete
        flextype('emitter')->emit('onEntryDelete');

        return Filesystem::deleteDir($this->getDirLocation($id));
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
        // Run event: onEntryRename
        flextype('emitter')->emit('onEntryCopy');

        return Filesystem::copy($this->getDirLocation($id), $this->getDirLocation($new_id), $deep);
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
        return Filesystem::has($this->getFileLocation($id));
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
