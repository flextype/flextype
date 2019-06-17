<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Filesystem\Filesystem;

class Entries
{
    /**
     * Flextype Dependency Container
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
     * Fetch entry
     *
     * @access public
     * @param string $id Entry id
     * @return array|false The entry contents or false on failure.
     */
    public function fetch(string $id)
    {
        $entry_file = $this->_file_location($id);

        if (Filesystem::has($entry_file)) {

            // Create unique entry cache_id
            $cache_id = md5('entry' . $entry_file . ((Filesystem::getTimestamp($entry_file) === false) ? '' : Filesystem::getTimestamp($entry_file)));

            // Try to get the entry from cache
            if ($this->flextype['cache']->contains($cache_id)) {
                if ($entry_decoded = $this->flextype['cache']->fetch($cache_id)) {
                    return $entry_decoded;
                } else {
                    return false;
                }
            } else {
                if ($entry_body = Filesystem::read($entry_file)) {
                    if ($entry_decoded = JsonParser::decode($entry_body)) {

                        // Create default entry items
                        $entry_decoded['date'] = $entry_decoded['date'] ?? date($this->flextype['registry']->get('settings.date_format'), Filesystem::getTimestamp($entry_file));
                        $entry_decoded['slug'] = $entry_decoded['slug'] ?? ltrim(rtrim($id, '/'), '/');

                        // Save decoded entry content into the cache
                        $this->flextype['cache']->save($cache_id, $entry_decoded);

                        // Return decoded entry
                        return $entry_decoded;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Fetch all entries
     *
     * @access public
     * @param   string  $id         Entry id
     * @param   string  $order_by   Order by specific entry field.
     * @param   string  $order_type Order type: DESC or ASC
     * @param   int     $offset     Offset
     * @param   int     $length     Length
     * @param   bool    $recursive  Whether to list recursively.
     * @return array The entries
     */
    public function fetchAll(string $id, string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null, bool $recursive = false) : array
    {
        // Set empty entries array where founded entries will stored
        $entries = [];

        // Set empty cache id for the entries
        $cache_id = '';

        // Get entries path
        $entries_path = $this->_dir_location($id);

        // Get entries list
        $entries_list = Filesystem::listContents($entries_path, $recursive);

        // Create unique entries cache_id
        foreach ($entries_list as $current_entry) {
            if (strpos($current_entry['path'], $id . '/entry.json') !== false) {
                // ignore ...
            } else {
                if ($current_entry['type'] == 'dir' && Filesystem::has($current_entry['path'] . '/entry.json')) {
                    $cache_id .= md5('entries' . $current_entry['path'] . Filesystem::getTimestamp($current_entry['path'] . '/entry.json'));
                }
            }
        }

        // If the entries exist at a specific cache_id,
        // then we take them from the cache otherwise we look for them.
        if ($this->flextype['cache']->contains($cache_id)) {
            $entries = $this->flextype['cache']->fetch($cache_id);
        } else {

            // Create entries array from entries list and ignore current requested entry
            foreach ($entries_list as $current_entry) {
                if (strpos($current_entry['path'], $id . '/entry.json') !== false) {
                    // ignore ...
                } else {
                    // We are checking...
                    // Whether the requested entry is a director and whether the file entry.json is in this directory.
                    if ($current_entry['type'] == 'dir' && Filesystem::has($current_entry['path'] . '/entry.json')) {

                        // Get entry uid
                        // 1. Remove entries path
                        // 2. Remove left and right slashes
                        $uid = ltrim(rtrim(str_replace(PATH['entries'], '', $current_entry['path']), '/'), '/');

                        // For each founded entry we should create $entries array.
                        $entries[$uid] = $this->fetch($uid);
                    }
                }
            }

            // Save entries into the cache
            $this->flextype['cache']->save($cache_id, $entries);
        }

        // If count of the entries more then 0 then sort and slice them.
        if (count($entries) > 0) {

            // Sort entries
            $entries = Arr::sort($entries, $order_by, $order_type);

            // Slice entries
            if ($offset !== null && $length !== null) {
                $entries = array_slice($entries, $offset, $length);
            }
        }
        
        // Return entries
        return $entries;
    }

    /**
     * Rename entry.
     *
     * @access public
     * @param string $id     Entry id
     * @param string $new_id New entry id
     * @return bool True on success, false on failure.
     */
    public function rename(string $id, string $new_id) : bool
    {
        return rename($this->_dir_location($id), $this->_dir_location($new_id));
    }

    /**
     * Update entry
     *
     * @access public
     * @param string $id    Entry
     * @param array  $data  Data
     * @return bool
     */
    public function update(string $id, array $data) : bool
    {
        $entry_file = $this->_file_location($id);

        if (Filesystem::has($entry_file)) {
            return Filesystem::write($entry_file, JsonParser::encode($data));
        } else {
            return false;
        }
    }

    /**
     * Create entry
     *
     * @access public
     * @param string $id    Entry id
     * @param array  $data  Data
     * @return bool
     */
    public function create(string $id, array $data) : bool
    {
        $entry_dir = $this->_dir_location($id);

        // Check if new entry directory exists
        if (!Filesystem::has($entry_dir)) {

            // Try to create directory for new entry
            if (Filesystem::createDir($entry_dir)) {

                // Entry file path
                $entry_file = $entry_dir . '/entry.json';

                // Check if new entry file exists
                if (!Filesystem::has($entry_file)) {
                    return Filesystem::write($entry_file, JsonParser::encode($data));
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Delete entry.
     *
     * @access public
     * @param string $id Entry id
     * @return bool True on success, false on failure.
     */
    public function delete(string $id) : bool
    {
        return Filesystem::deleteDir($this->_dir_location($id));
    }

    /**
     * Copy entry(s)
     *
     * @access public
     * @param string $id      Entry id
     * @param string $new_id  New entry id
     * @param bool   $recursive  Recursive copy entries.
     * @return bool True on success, false on failure.
     */
    public function copy(string $id, string $new_id, bool $recursive = false)
    {
        return Filesystem::copy($this->_dir_location($id), $this->_dir_location($new_id), $recursive);
    }

    /**
     * Check whether entry exists.
     *
     * @access public
     * @param string $id Entry
     * @return bool
     */
    public function has(string $id) : bool
    {
        return Filesystem::has($this->_file_location($id));
    }

    /**
     * Helper method _file_location
     *
     * @access private
     * @param string $id Entry id
     * @return string
     */
    private function _file_location(string $id) : string
    {
        return PATH['entries'] . '/' . $id . '/entry.json';
    }

    /**
     * Helper method _dir_location
     *
     * @access private
     * @param string $id Entry id
     * @return string
     */
    private function _dir_location(string $id) : string
    {
        return PATH['entries'] . '/' . $id;
    }
}
