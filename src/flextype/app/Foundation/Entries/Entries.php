<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation\Entries;

use Flextype\Component\Filesystem\Filesystem;
use function array_merge;
use function count;
use function ltrim;
use function md5;
use function rename;
use function rtrim;
use function str_replace;
use function strpos;

class Entries
{
    /**
     * Current entry path
     *
     * @var string
     * @access public
     */
    public $entry_path = null;

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
     * Current entry create data array
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
     * Current entries path
     *
     * @var string
     * @access public
     */
    public $entries_path = null;

    /**
     * Flextype Dependency Container
     *
     * @access private
     */
    private $flextype;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Fetch entry(entries)
     *
     * @param string $path       Unique identifier of the entry(entries).
     * @param bool   $collection Set `true` if collection of entries need to be fetched.
     * @param array  $filter     Select items in collection by given conditions.
     *
     * @return array The entry array data.
     *
     * @access public
     */
    public function fetch(string $path, bool $collection = false, $filter = []) : array
    {
        if ($collection) {
            return $this->fetchCollection($path, $filter);
        }

        return $this->fetchSingle($path);
    }

    /**
     * Fetch single entry
     *
     * @param string $path Unique identifier of the entry(entries).
     *
     * @return array The entry array data.
     *
     * @access public
     */
    public function fetchSingle(string $path) : array
    {
        // Store current requested entry path
        $this->entry_path = $path;

        // Get Cache ID for current requested entry
        $entry_cache_id = $this->getCacheID($this->entry_path);

        // Try to get current requested entry from cache
        if ($this->flextype['cache']->contains($entry_cache_id)) {
            // Fetch entry from cache
            $this->entry = $this->flextype['cache']->fetch($entry_cache_id);

            // Run event: onEntryAfterCacheInitialized
            $this->flextype['emitter']->emit('onEntryAfterCacheInitialized');

            // Return entry from cache
            return $this->entry;
        }

        // Try to get current requested entry from filesystem
        if ($this->has($this->entry_path)) {
            // Get entry file location
            $entry_file = $this->getFileLocation($this->entry_path);

            // Try to get requested entry from the filesystem
            $entry_file_content = Filesystem::read($entry_file);
            if ($entry_file_content === false) {
                return [];
            }

            // Decode entry file content
            $this->entry = $this->flextype['frontmatter']->decode($entry_file_content);

            // Run event: onEntryAfterInitialized
            $this->flextype['emitter']->emit('onEntryAfterInitialized');

            // Set cache state
            $cache = $this->flextype['entries']->entry['cache']['enabled'] ??
                                $this->flextype['registry']->get('flextype.settings.cache.enabled');

            // Save entry data to cache
            if ($cache) {
                $this->flextype['cache']->save($entry_cache_id, $this->entry);
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
     * @param string $path   Unique identifier of the entry(entries).
     * @param array  $filter Select items in collection by given conditions.
     *
     * @return array|bool|int
     *
     * @access public
     */
    public function fetchCollection(string $path, $filter = [])
    {
        // Init Entries object
        $this->entries = [];

        // Store current requested entries path
        $this->entries_path = $this->getDirLocation($path);

        // Find entries
        $entries_list = find_filter($this->entries_path, $filter);

        // If entries founded in the entries folder
        // We are checking... Whether the requested entry is an a true entry.
        // Get entry uid. Remove entries path and remove left and right slashes.
        // Fetch single entry.
        if (count($entries_list) > 0) {
            foreach ($entries_list as $current_entry) {
                if ($current_entry->getType() === 'file' && $current_entry->getFilename() === 'entry' . '.' . $this->flextype->registry->get('flextype.settings.entries.extension')) {
                    $uid = ltrim(rtrim(str_replace(PATH['project'] . '/entries/', '', $current_entry->getPath()), '/'), '/');
                    $this->entries[$uid] = $this->fetchSingle($uid);
                }
            }

            // Apply collection filter
            $this->entries = collect_filter($this->entries, $filter);

            // Run event: onEntriesAfterInitialized
            $this->flextype['emitter']->emit('onEntriesAfterInitialized');
        }

        // Return entries array
        return $this->entries;
    }

    /**
     * Rename entry
     *
     * @param string $path     Unique identifier of the entry(entries).
     * @param string $new_path New Unique identifier of the entry(entries).
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function rename(string $path, string $new_path) : bool
    {
        if (! Filesystem::has($this->getDirLocation($new_path))) {
            // Run event: onEntryRename
            $this->flextype['emitter']->emit('onEntryRename');

            return rename($this->getDirLocation($path), $this->getDirLocation($new_path));
        }

        return false;
    }

    /**
     * Update entry
     *
     * @param string $path Unique identifier of the entry(entries).
     * @param array  $data Data to update for the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function update(string $path, array $data) : bool
    {
        $entry_file = $this->getFileLocation($path);

        if (Filesystem::has($entry_file)) {
            $body  = Filesystem::read($entry_file);
            $entry = $this->flextype['frontmatter']->decode($body);

            // Store data in the entry_update_data
            $this->entry_update_data = $data;

            // Run event: onEntryUpdate
            $this->flextype['emitter']->emit('onEntryUpdate');

            return Filesystem::write($entry_file, $this->flextype['frontmatter']->encode(array_merge($entry, $this->entry_update_data)));
        }

        return false;
    }

    /**
     * Create entry
     *
     * @param string $path Unique identifier of the entry(entries).
     * @param array  $data Data to create for the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function create(string $path, array $data) : bool
    {
        $entry_dir = $this->getDirLocation($path);

        if (! Filesystem::has($entry_dir)) {
            // Try to create directory for new entry
            if (Filesystem::createDir($entry_dir)) {
                // Check if new entry file exists
                if (! Filesystem::has($entry_file = $entry_dir . '/entry' . '.' . $this->flextype->registry->get('flextype.settings.entries.extension'))) {
                    // Store data in the entry_create_data
                    $this->entry_create_data = $data;

                    // Run event: onEntryCreate
                    $this->flextype['emitter']->emit('onEntryCreate');

                    // Create a new entry!
                    return Filesystem::write($entry_file, $this->flextype['frontmatter']->encode($this->entry_create_data));
                }

                return false;
            }
        }

        return false;
    }

    /**
     * Delete entry
     *
     * @param string $path Unique identifier of the entry(entries).
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $path) : bool
    {
        // Run event: onEntryDelete
        $this->flextype['emitter']->emit('onEntryDelete');

        return Filesystem::deleteDir($this->getDirLocation($path));
    }

    /**
     * Copy entry(s)
     *
     * @param string $path     Unique identifier of the entry(entries).
     * @param string $new_path New Unique identifier of the entry(entries).
     * @param bool   $deep     Recursive copy entries.
     *
     * @return bool|null True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $path, string $new_path, bool $deep = false) : ?bool
    {
        // Run event: onEntryRename
        $this->flextype['emitter']->emit('onEntryCopy');

        return Filesystem::copy($this->getDirLocation($path), $this->getDirLocation($new_path), $deep);
    }

    /**
     * Check whether entry exists
     *
     * @param string $path Unique identifier of the entry(entries).
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function has(string $path) : bool
    {
        return Filesystem::has($this->getFileLocation($path));
    }

    /**
     * Get entry file location
     *
     * @param string $path Unique identifier of the entry(entries).
     *
     * @return string entry file location
     *
     * @access public
     */
    public function getFileLocation(string $path) : string
    {
        return PATH['project'] . '/entries/' . $path . '/entry' . '.' . $this->flextype->registry->get('flextype.settings.entries.extension');
    }

    /**
     * Get entry directory location
     *
     * @param string $path Unique identifier of the entry(entries).
     *
     * @return string entry directory location
     *
     * @access public
     */
    public function getDirLocation(string $path) : string
    {
        return PATH['project'] . '/entries/' . $path;
    }

    /**
     * Get Cache ID for entry
     *
     * @param  string $path Unique identifier of the entry(entries).
     *
     * @return string Cache ID
     *
     * @access public
     */
    public function getCacheID(string $path) : string
    {
        if ($this->flextype['registry']->get('flextype.settings.cache.enabled') === false) {
            return '';
        }

        $entry_file = $this->getFileLocation($path);

        if (Filesystem::has($entry_file)) {
            return md5('entry' . $entry_file . Filesystem::getTimestamp($entry_file));
        }

        return md5('entry' . $entry_file);
    }
}
