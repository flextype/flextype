<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained Flextype Community.
 */

namespace Flextype;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Flextype\Component\Filesystem\Filesystem;
use function ltrim;
use function md5;
use function rename;
use function rtrim;
use function str_replace;
use function strpos;

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
     * Fetch single entry
     *
     * @param string $id Entry ID
     *
     * @return array|false The entry contents or false on failure.
     *
     * @access public
     */
    public function fetch(string $id)
    {
        // Get entry file location
        $entry_file = $this->_file_location($id);

        // If requested entry founded then process it
        if ($entry_file) {
            // Create unique entry cache_id
            // Entry Cache ID = entry + entry file + entry file time stamp
            if ($timestamp = Filesystem::getTimestamp($entry_file['file'])) {
                $entry_cache_id = md5('entry' . $entry_file['file'] . $timestamp);
            } else {
                $entry_cache_id = md5('entry' . $entry_file['file']);
            }

            // Try to get the requested entry from cache
            if ($this->flextype['cache']->contains($entry_cache_id)) {
                // Try to fetch requested entry from the cache
                if ($entry = $this->flextype['cache']->fetch($entry_cache_id)) {
                    // Run event onEntryAfterInitialized
                    $this->flextype['emitter']->emit('onEntryAfterInitialized');

                    // Return entry
                    return $entry;
                }

                return false;

            // else Try to get requested entry from the filesystem
            }

            // Try to get requested entry body content
            if ($entry_body = Filesystem::read($entry_file['file'])) {
                // Try to decode requested entry body content
                if ($entry_decoded = Parser::decode($entry_body, $entry_file['driver'])) {
                    // Add predefined entry items
                    // Entry Date
                    $entry_decoded['published_at'] = $entry_decoded['published_at'] ? $entry_decoded['published_at'] : Filesystem::getTimestamp($entry_file['file']);
                    $entry_decoded['created_at']   = $entry_decoded['created_at'] ? $entry_decoded['created_at'] : Filesystem::getTimestamp($entry_file['file']);

                    // Entry Timestamp
                    $entry_decoded['modified_at'] = Filesystem::getTimestamp($entry_file['file']);

                    // Entry Slug
                    $entry_decoded['slug'] = $entry_decoded['slug'] ?? ltrim(rtrim($id, '/'), '/');

                    // Save decoded entry content into the cache
                    $this->flextype['cache']->save($entry_cache_id, $entry_decoded);

                    // Set entry to the Entry class property $entry
                    $this->entry = $entry_decoded;

                    // Run event onEntryAfterInitialized
                    $this->flextype['emitter']->emit('onEntryAfterInitialized');

                    // Return entry from the Entry class property $entry
                    return $this->entry;
                }

                return false;
            }

            return false;
        }

        return false;
    }

    /**
     * Fetch entries collection
     *
     * @param array $args Query arguments
     *
     * @return array The entries
     *
     * @access public
     */
    public function fetchAll(string $id, array $args = []) : array
    {
        // Init Entries
        $entries = [];

        // Set Expression
        $expression = [
            '=' => Comparison::EQ,
            '<>' => Comparison::NEQ,
            '<' => Comparison::LT,
            '<=' => Comparison::LTE,
            '>' => Comparison::GT,
            '>=' => Comparison::GTE,
            'is' => Comparison::IS,
            'in' => Comparison::IN,
            'nin' => Comparison::NIN,
            'contains' => Comparison::CONTAINS,
            'member_of' => Comparison::MEMBER_OF,
            'start_with' => Comparison::STARTS_WITH,
            'ends_with' => Comparison::ENDS_WITH,
        ];

        // Set Direction
        $direction = [
            'asc' => Criteria::ASC,
            'desc' => Criteria::DESC,
        ];

        // Bind: entry id
        $bind_id = $id;

        // Bind: recursive
        $bind_recursive = $args['recursive'] ?? false;

        // Bind: set first result
        $bind_set_first_result = $args['set_first_result'] ?? false;

        // Bind: set max result
        $bind_set_max_result = $args['set_max_result'] ?? false;

        // Bind: where
        if (isset($args['where']['key']) && isset($args['where']['expr']) && isset($args['where']['value'])) {
            $bind_where                   = [];
            $bind_where['where']['key']   = $args['where']['key'];
            $bind_where['where']['expr']  = $expression[$args['where']['expr']];
            $bind_where['where']['value'] = $args['where']['value'];
        } else {
            $bind_where = false;
        }

        // Bind: and where
        if (isset($args['and_where']['key']) && isset($args['and_where']['expr']) && isset($args['and_where']['value'])) {
            $bind_and_where                       = [];
            $bind_and_where['and_where']['key']   = $args['and_where']['key'];
            $bind_and_where['and_where']['expr']  = $expression[$args['and_where']['expr']];
            $bind_and_where['and_where']['value'] = $args['and_where']['value'];
        } else {
            $bind_and_where = false;
        }

        // Bind: or where
        if (isset($args['or_where']['key']) && isset($args['or_where']['expr']) && isset($args['or_where']['value'])) {
            $bind_or_where                      = [];
            $bind_or_where['or_where']['key']   = $args['or_where']['key'];
            $bind_or_where['or_where']['expr']  = $expression[$args['or_where']['expr']];
            $bind_or_where['or_where']['value'] = $args['or_where']['value'];
        } else {
            $bind_or_where = false;
        }

        // Bind: order by
        if (isset($args['order_by']['field']) && isset($args['order_by']['direction'])) {
            $bind_order_by                          = [];
            $bind_order_by['order_by']['field']     = $args['order_by']['field'];
            $bind_order_by['order_by']['direction'] = $args['order_by']['direction'];
        } else {
            $bind_order_by = false;
        }

        // Get entries path
        $entries_path = $this->_dir_location($bind_id);

        // Get entries list
        $entries_list = Filesystem::listContents($entries_path, $bind_recursive);

        // Create unique entries $_entries_ids
        // 1. Go through all entries
        // 2. set all entries IDs and their timestamps into the $_entries_ids
        $_entries_ids = '';
        foreach ($entries_list as $current_entry) {
            if (strpos($current_entry['path'], $bind_id . '/entry.json') !== false) {
                // ignore ...
            } else {
                if ($current_entry['type'] === 'dir' && Filesystem::has($current_entry['path'] . '/entry.json')) {
                    if ($timestamp = Filesystem::getTimestamp($current_entry['path'] . '/entry.json')) {
                        $_entries_ids .= 'entry:' . ltrim(rtrim(str_replace(PATH['entries'], '', $current_entry['path']), '/'), '/') . ' timestamp:' . $timestamp;
                    } else {
                        $_entries_ids .= 'entry:' . ltrim(rtrim(str_replace(PATH['entries'], '', $current_entry['path']), '/'), '/');
                    }
                }
            }
        }

        // Create unique entries $cache_id
        $cache_id =  md5($_entries_ids .
                         $bind_id .
                         ($bind_recursive ? 'true' : 'false') .
                         ($bind_set_max_result ? $bind_set_max_result : 'false') .
                         ($bind_set_first_result ? $bind_set_first_result : 'false') .
                         ($bind_where['where']['key'] ? $bind_where['where']['key'] : 'false') .
                         ($bind_where['where']['expr'] ? $bind_where['where']['expr'] : 'false') .
                         ($bind_where['where']['value'] ? $bind_where['where']['value'] : 'false') .
                         ($bind_and_where['and_where']['key'] ? $bind_and_where['and_where']['key'] : 'false') .
                         ($bind_and_where['and_where']['expr'] ? $bind_and_where['and_where']['expr'] : 'false') .
                         ($bind_and_where['and_where']['value'] ? $bind_and_where['and_where']['value'] : 'false') .
                         ($bind_or_where['or_where']['key'] ? $bind_or_where['or_where']['key'] : 'false') .
                         ($bind_or_where['or_where']['expr'] ? $bind_or_where['or_where']['expr'] : 'false') .
                         ($bind_or_where['or_where']['value'] ? $bind_or_where['or_where']['value'] : 'false') .
                         ($bind_order_by['order_by']['field'] ? $bind_order_by['order_by']['field'] : 'false') .
                         ($bind_order_by['order_by']['direction'] ? $bind_order_by['order_by']['direction'] : 'false'));

        // If requested entries exist with a specific cache_id,
        // then we take them from the cache otherwise we look for them.
        if ($this->flextype['cache']->contains($cache_id)) {
            $entries = $this->flextype['cache']->fetch($cache_id);
        } else {
            // Create entries array from entries list and ignore current requested entry
            foreach ($entries_list as $current_entry) {
                if (strpos($current_entry['path'], $bind_id . '/entry.json') !== false) {
                    // ignore ...
                } else {
                    // We are checking...
                    // Whether the requested entry is a director and whether the file entry.json is in this directory.
                    if ($current_entry['type'] === 'dir' && Filesystem::has($current_entry['path'] . '/entry.json')) {
                        // Get entry uid
                        // 1. Remove entries path
                        // 2. Remove left and right slashes
                        $uid = ltrim(rtrim(str_replace(PATH['entries'], '', $current_entry['path']), '/'), '/');

                        // For each founded entry we should create $entries array.
                        $entry = $this->fetch($uid);

                        // Add entry into the entries
                        $entries[$uid] = $entry;
                    }
                }
            }

            // Create Array Collection from entries array
            $collection = new ArrayCollection($entries);

            // Create Criteria for filtering Selectable collections.
            $criteria = new Criteria();

            // Exec: where
            if (isset($bind_and_where['where']['key']) && isset($bind_and_where['where']['expr']) && isset($bind_and_where['where']['value'])) {
                $expr = new Comparison($bind_where['where']['key'], $bind_where['where']['expr'], $bind_where['where']['value']);
                $criteria->where($expr);
            }

            // Exec: and where
            if (isset($bind_and_where['and_where']['key']) && isset($bind_and_where['and_where']['expr']) && isset($bind_and_where['and_where']['value'])) {
                $expr = new Comparison($bind_and_where['and_where']['key'], $bind_and_where['and_where']['expr'], $bind_and_where['and_where']['value']);
                $criteria->where($expr);
            }

            // Exec: or where
            if (isset($bind_or_where['or_where']['key']) && isset($bind_or_where['or_where']['expr']) && isset($bind_or_where['or_where']['value'])) {
                $expr = new Comparison($bind_or_where['or_where']['key'], $bind_or_where['or_where']['expr'], $bind_or_where['or_where']['value']);
                $criteria->where($expr);
            }

            // Exec: order by
            if (isset($bind_order_by['order_by']['field']) && isset($bind_order_by['order_by']['direction'])) {
                $criteria->orderBy([$bind_order_by['order_by']['field'] => $direction[$bind_order_by['order_by']['direction']]]);
            }

            // Exec: set max result
            if ($bind_set_max_result) {
                $criteria->setMaxResults($bind_set_max_result);
            }

            // Exec: set first result
            if ($bind_set_first_result) {
                $criteria->setFirstResult($bind_set_first_result);
            }

            // Get entries for matching criterias
            $entries = $collection->matching($criteria);

            // Gets a native PHP array representation of the collection.
            $entries = $entries->toArray();

            // Save entries into the cache
            $this->flextype['cache']->save($cache_id, $entries);
        }

        // Set entries into the property entries
        $this->entries = $entries;

        // Run event onEntriesAfterInitialized
        $this->flextype['emitter']->emit('onEntriesAfterInitialized');

        // Return entries
        return $this->entries;
    }

    /**
     * Rename entry.
     *
     * @param string $id     Entry id
     * @param string $new_id New entry id
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function rename(string $id, string $new_id) : bool
    {
        return rename($this->_dir_location($id), $this->_dir_location($new_id));
    }

    /**
     * Update entry
     *
     * @param string $id   Entry
     * @param array  $data Data
     *
     * @access public
     */
    public function update(string $id, array $data) : bool
    {
        $entry_file = $this->_file_location($id);

        if (Filesystem::has($entry_file)) {
            return Filesystem::write($entry_file, JsonParser::encode($data));
        }

        return false;
    }

    /**
     * Create entry
     *
     * @param string $id   Entry id
     * @param array  $data Data
     *
     * @access public
     */
    public function create(string $id, array $data) : bool
    {
        $entry_dir = $this->_dir_location($id);

        // Check if new entry directory exists
        if (! Filesystem::has($entry_dir)) {
            // Try to create directory for new entry
            if (Filesystem::createDir($entry_dir)) {
                // Entry file path
                $entry_file = $entry_dir . '/entry.json';

                // Check if new entry file exists
                if (! Filesystem::has($entry_file)) {
                    return Filesystem::write($entry_file, JsonParser::encode($data));
                }

                return false;
            }

            return false;
        }

        return false;
    }

    /**
     * Delete entry.
     *
     * @param string $id Entry id
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id) : bool
    {
        return Filesystem::deleteDir($this->_dir_location($id));
    }

    /**
     * Copy entry(s)
     *
     * @param string $id        Entry id
     * @param string $new_id    New entry id
     * @param bool   $recursive Recursive copy entries.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $new_id, bool $recursive = false) : bool
    {
        return Filesystem::copy($this->_dir_location($id), $this->_dir_location($new_id), $recursive);
    }

    /**
     * Check whether entry exists.
     *
     * @param string $id Entry
     *
     * @access public
     */
    public function has(string $id) : bool
    {
        return Filesystem::has($this->_file_location($id));
    }

    /**
     * Helper method _file_location
     *
     * @param string $id Entry id
     *
     * @access private
     */
    private function _file_location(string $id)
    {
        foreach (Parser::$drivers as $driver) {
            $driver_file = PATH['entries'] . '/' . $id . '/entry' . '.' . $driver['ext'];

            if (Filesystem::has($driver_file)) {
                return [
                    'file' => $driver_file,
                    'driver' => $driver['name'],
                ];
            }
        }

        return false;
    }

    /**
     * Helper method _dir_location
     *
     * @param string $id Entry id
     *
     * @access private
     */
    private function _dir_location(string $id) : string
    {
        return PATH['entries'] . '/' . $id;
    }
}
