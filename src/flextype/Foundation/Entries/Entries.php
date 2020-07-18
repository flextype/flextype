<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Ramsey\Uuid\Uuid;
use function array_merge;
use function count;
use function date;
use function in_array;
use function is_array;
use function is_bool;
use function ltrim;
use function md5;
use function rename;
use function rtrim;
use function str_replace;
use function strpos;
use function strtotime;
use function time;

class Entries
{
    /**
     * Current entry data array
     *
     * @var array
     * @access public
     */
    public $entry = [];

    /**
     * Current entries data array
     *
     * @var array
     * @access public
     */
    public $entries = [];

    /**
     * Entries Visibility
     *
     * @var array
     * @access public
     */
    public $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    /**
     * Entries system fields
     *
     * @var array
     * @access public
     */
    public $system_fields = [
        'published_at' => 'published_at',
        'published_by' => 'published_by',
        'created_at' => 'created_at',
        'modified_at' => 'modified_at',
        'slug' => 'slug',
        'routable' => 'routable',
        'parsers' => 'parsers',
        'visibility' => 'visibility',
    ];

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
     * @param string     $path   Unique identifier of the entry(entries).
     * @param array|null $filter Select items in collection by given conditions.
     *
     * @return array The entry array data.
     *
     * @access public
     */
    public function fetch(string $path, ?array $filter = null) : array
    {
        // If filter is array then it is entries collection request
        if (is_array($filter)) {
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
        // Get entry file location
        $entry_file = $this->getFileLocation($path);

        // If requested entry file founded then process it
        if (Filesystem::has($entry_file)) {

            // Create unique entry cache_id
            // Entry Cache ID = entry + entry file + entry file time stamp
            if ($timestamp = Filesystem::getTimestamp($entry_file)) {
                $entry_cache_id = md5('entry' . $entry_file . $timestamp);
            } else {
                $entry_cache_id = md5('entry' . $entry_file);
            }

            // 1. Try to get the requested entry from cache
            // 2. Try to fetch requested entry from the cache
            // 3. Run event: onEntryAfterInitialized
            // 4. Return entry item array
            // 5. Else return empty array
            if ($this->flextype['cache']->contains($entry_cache_id)) {
                if ($entry = $this->flextype['cache']->fetch($entry_cache_id)) {
                    $this->flextype['emitter']->emit('onEntryAfterInitialized');
                    return $entry;
                }
                return [];
            }

            // Try to get requested entry from the filesystem
            $entry_decoded = $this->flextype['serializer']->decode(Filesystem::read($entry_file), 'frontmatter');

            //
            // Set system entry fields

            // Entry Published At
            $entry_decoded['published_at'] = isset($entry_decoded['published_at']) ? (int) strtotime($entry_decoded['published_at']) : (int) Filesystem::getTimestamp($entry_file);

            // Entry Created At
            $entry_decoded['created_at'] = isset($entry_decoded['created_at']) ? (int) strtotime($entry_decoded['created_at']) : (int) Filesystem::getTimestamp($entry_file);

            // Entry Modified
            $entry_decoded['modified_at'] = (int) Filesystem::getTimestamp($entry_file);

            // Entry Slug
            $entry_decoded['slug'] = isset($entry_decoded['slug']) ? (string) $entry_decoded['slug'] : (string) ltrim(rtrim($path, '/'), '/');

            // Entry Routable
            $entry_decoded['routable'] = isset($entry_decoded['routable']) ? (bool) $entry_decoded['routable'] : true;

            // Entry Visibility
            if (isset($entry_decoded['visibility']) && in_array($entry_decoded['visibility'], $this->visibility)) {
                $entry_decoded['visibility'] = (string) $this->visibility[$entry_decoded['visibility']];
            } else {
                $entry_decoded['visibility'] = (string) $this->visibility['visible'];
            }

            // Parsers
            if (isset($entry_decoded['parsers'])) {
                foreach ($entry_decoded['parsers'] as $parser_name => $parser_data) {
                    if (! in_array($parser_name, ['markdown', 'shortcodes'])) {
                        continue;
                    }

                    if (! isset($entry_decoded['parsers'][$parser_name]['enabled']) || $entry_decoded['parsers'][$parser_name]['enabled'] !== true) {
                        continue;
                    }

                    if (isset($entry_decoded['parsers'][$parser_name]['cache']) && $entry_decoded['parsers'][$parser_name]['cache'] === true) {
                        $cache = true;
                    } else {
                        $cache = false;
                    }

                    if (! isset($entry_decoded['parsers'][$parser_name]['fields'])) {
                        continue;
                    }

                    if (! is_array($entry_decoded['parsers'][$parser_name]['fields'])) {
                        continue;
                    }

                    foreach ($entry_decoded['parsers'][$parser_name]['fields'] as $field) {
                        if (in_array($field, $this->system_fields)) {
                            continue;
                        }

                        if ($parser_name === 'markdown') {
                            if (Arr::keyExists($entry_decoded, $field)) {
                                Arr::set($entry_decoded, $field, $this->flextype['parser']->parse(Arr::get($entry_decoded, $field), 'markdown', $cache));
                            }
                        }

                        if ($parser_name !== 'shortcodes') {
                            continue;
                        }

                        if (! Arr::keyExists($entry_decoded, $field)) {
                            continue;
                        }

                        Arr::set($entry_decoded, $field, $this->flextype['parser']->parse(Arr::get($entry_decoded, $field), 'shortcodes', $cache));
                    }
                }
            }

            // Save decoded entry content into the cache
            $this->flextype['cache']->save($entry_cache_id, $entry_decoded);

            // Set entry to the Entry class property $entry
            $this->entry = $entry_decoded;

            // Run event onEntryAfterInitialized
            $this->flextype['emitter']->emit('onEntryAfterInitialized');

            // Return entry from the Entry class property $entry
            return $this->entry;
        }

        // Return empty array
        return [];
    }

    /**
     * Fetch entries collection
     *
     * @param string $path Unique identifier of the entry(entries).
     * @param array  $recursive
     *
     * @return array The entries array data.
     *
     * @access public
     */
    public function fetchCollection(string $path, $recursive = false) : array
    {
        // Init Entries
        $entries = [];

        // Init Entries
        $this->entries = $entries;

        // Get entries path
        $entries_path = $this->getDirLocation($path);

        // Get entries list
        $entries_list = Filesystem::listContents($entries_path, $recursive);

        // If entries founded in entries folder
        if (count($entries_list) > 0) {
            // Create entries array from entries list and ignore current requested entry
            foreach ($entries_list as $current_entry) {
                if (strpos($current_entry['path'], $path . '/entry' . '.' . $this->flextype->registry->get('flextype.settings.entries.extension')) !== false) {
                    // ignore ...
                } else {
                    // We are checking...
                    // Whether the requested entry is a director and whether the file entry is in this directory.
                    if ($current_entry['type'] === 'dir' && Filesystem::has($current_entry['path'] . '/entry' . '.' . $this->flextype->registry->get('flextype.settings.entries.extension'))) {
                        // Get entry uid
                        // 1. Remove entries path
                        // 2. Remove left and right slashes
                        $uid = ltrim(rtrim(str_replace(PATH['project'] . '/entries/', '', $current_entry['path']), '/'), '/');

                        // Fetch single entry
                        $entries[$uid] = $this->fetch($uid);
                    }
                }
            }

            // Save entries array into the property entries
            $this->entries = $entries;

            // Run event: onEntriesAfterInitialized
            $this->flextype['emitter']->emit('onEntriesAfterInitialized');
        }

        // Return entries array
        return $this->entries;
    }

    /**
     * Rename entry
     *
     * @param string $path   Unique identifier of the entry(entries).
     * @param string $new_id New Unique identifier of the entry(entries).
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function rename(string $path, string $new_id) : bool
    {
        if (! Filesystem::has($this->getDirLocation($new_id))) {
            return rename($this->getDirLocation($path), $this->getDirLocation($new_id));
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
            $entry = $this->flextype['serializer']->decode($body, 'frontmatter');

            return Filesystem::write($entry_file, $this->flextype['serializer']->encode(array_merge($entry, $data), 'frontmatter'));
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
                    $data['uuid']         = Uuid::uuid4()->toString();
                    $data['published_at'] = date($this->flextype->registry->get('flextype.settings.date_format'), time());
                    $data['created_at']   = date($this->flextype->registry->get('flextype.settings.date_format'), time());
                    $data['published_by'] = (Session::exists('uuid') ? Session::get('uuid') : '');
                    $data['created_by']   = (Session::exists('uuid') ? Session::get('uuid') : '');

                    if (isset($data['routable']) && is_bool($data['routable'])) {
                        $data['routable'] = $data['routable'];
                    } else {
                        $data['routable'] = true;
                    }

                    if (isset($data['visibility']) && in_array($data['visibility'], $this->visibility)) {
                        $data['visibility'] = $data['visibility'];
                    } else {
                        $data['visibility'] = 'visible';
                    }

                    return Filesystem::write($entry_file, $this->flextype['serializer']->encode($data, 'frontmatter'));
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
        return Filesystem::deleteDir($this->getDirLocation($path));
    }

    /**
     * Copy entry(s)
     *
     * @param string $path      Unique identifier of the entry(entries).
     * @param string $new_id    New Unique identifier of the entry(entries).
     * @param bool   $recursive Recursive copy entries.
     *
     * @return bool|null True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $path, string $new_id, bool $recursive = false) : ?bool
    {
        return Filesystem::copy($this->getDirLocation($path), $this->getDirLocation($new_id), $recursive);
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
     * @access private
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
     * @access private
     */
    public function getDirLocation(string $path) : string
    {
        return PATH['project'] . '/entries/' . $path;
    }
}
