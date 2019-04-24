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
     * @param string $entry Entry
     * @return array|false The entry contents or false on failure.
     */
    public function fetch(string $entry)
    {

        $entry_file = Entries::_file_location($entry);

        if (Filesystem::has($entry_file)) {

            $cache_id = md5('entry' . $entry_file . ((Filesystem::getTimestamp($entry_file) === false) ? '' : Filesystem::getTimestamp($entry_file)));

            // Try to get the entry from cache
            if ($this->flextype['cache']->contains($cache_id)) {
                if ($entry_decoded = $this->flextype['cache']->fetch($cache_id)) {

                    // Apply Shortcodes for each entry fields
                    foreach ($entry_decoded as $key => $_entry_decoded) {
                        $entry_decoded[$key] = $this->flextype['shortcodes']->process($_entry_decoded);
                    }

                    return $entry_decoded;
                } else {
                    return false;
                }
            } else {

                if ($entry_body = Filesystem::read($entry_file)) {
                    if ($entry_decoded = JsonParser::decode($entry_body)) {

                        // Create default entry items
                        $entry_decoded['date'] = $entry_decoded['date'] ?? date($this->flextype['registry']->get('settings.date_format'), Filesystem::getTimestamp($entry_file));
                        $entry_decoded['slug'] = $entry_decoded['slug'] ?? ltrim(rtrim($entry, '/'), '/');

                        // Save to cache
                        $this->flextype['cache']->save($cache_id, $entry_decoded);

                        // Apply Shortcodes for each entry fields
                        foreach ($entry_decoded as $key => $_entry_decoded) {
                            $entry_decoded[$key] = $this->flextype['shortcodes']->process($_entry_decoded);
                        }

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
     * Fetch entries
     *
     * @access public
     * @param   string  $entry      Entry
     * @param   string  $order_by   Order by specific entry field.
     * @param   string  $order_type Order type: DESC or ASC
     * @param   int     $offset     Offset
     * @param   int     $length     Length
     * @return array The entries
     */
    public function fetchAll(string $entry, string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null) : array
    {
        // Entries array where founded entries will stored
        $entries = [];

        // Сache id
        $cache_id = '';

        // Entries path
        $entries_path = $this->_dir_location($entry);

        // Get entries list
        $entries_list = Filesystem::listContents($entries_path);

        // Create entries cached id
        foreach ($entries_list as $current_entry) {
            if (strpos($current_entry['path'], $entry . '/entry.json') !== false) {
                // ignore ...
            } else {
                if ($current_entry['type'] == 'dir' && Filesystem::has($current_entry['path'] . '/entry.json')) {
                    $cache_id .= md5('entries' . $current_entry['path'] . Filesystem::getTimestamp($current_entry['path'] . '/entry.json'));
                }
            }
        }

        if ($this->flextype['cache']->contains($cache_id)) {
            $entries = $this->flextype['cache']->fetch($cache_id);
        } else {

            // Create entries array from entries list and ignore current requested entry
            foreach ($entries_list as $current_entry) {
                if (strpos($current_entry['path'], $entry . '/entry.json') !== false) {
                    // ignore ...
                } else {
                    if ($current_entry['type'] == 'dir' && Filesystem::has($current_entry['path'] . '/entry.json')) {
                        $entries[$current_entry['dirname']] = $this->fetch($entry . '/' . $current_entry['dirname']);
                    }
                }
            }

            $this->flextype['cache']->save($cache_id, $entries);
        }

        // Sort and Slice entries if $raw === false
        if (count($entries) > 0) {

            $entries = Arr::sort($entries, $order_by, $order_type);

            if ($offset !== null && $length !== null) {
                $entries = array_slice($entries, $offset, $length);
            }
        }

        return $entries;
    }

    /**
     * Rename entry.
     *
     * @access public
     * @param string $entry     Entry
     * @param string $new_entry New entry
     * @return bool True on success, false on failure.
     */
    public function rename(string $entry, string $new_entry) : bool
    {
        return rename($this->_dir_location($entry), $this->_dir_location($new_entry));
    }

    /**
     * Update entry
     *
     * @access public
     * @param string $entry Entry
     * @param array  $data  Data
     * @return bool
     */
    public function update(string $entry, array $data) : bool
    {
        $entry_file = $this->_file_location($entry);

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
     * @param string $entry Entry
     * @param array  $data  Data
     * @return bool
     */
    public function create(string $entry, array $data) : bool
    {
        $entry_dir = $this->_dir_location($entry);

        // Check if new entry directory exists
        if (!Filesystem::has($entry_dir)) {

            // Try to create directory for new entry
            if (Filesystem::createDir($entry_dir)) {

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
     * @param string $entry Entry
     * @return bool True on success, false on failure.
     */
    public function delete(string $entry) : bool
    {
        return Filesystem::deleteDir($this->_dir_location($entry));
    }

    /**
     * Copy entry(s)
     *
     * @access public
     * @param string $entry      Entry
     * @param string $new_entry  New entry
     * @param bool   $recursive  Recursive copy entries.
     * @return bool True on success, false on failure.
     */
    public function copy(string $entry, string $new_entry, bool $recursive = false) : bool
    {
        return Filesystem::copy($this->_dir_location($entry), $this->_dir_location($new_entry), $recursive);
    }

    /**
     * Check whether entry exists.
     *
     * @access public
     * @param string $entry Entry
     * @return bool
     */
    public function has(string $entry) : bool
    {
        return Filesystem::has($this->_file_location($entry));
    }

    /**
     * Helper method _file_location
     *
     * @access private
     * @param string $name Name
     * @return string
     */
    private function _file_location(string $name) : string
    {
        return PATH['entries'] . '/' . $name . '/entry.json';
    }

    /**
     * Helper method _dir_location
     *
     * @access private
     * @param string $name Name
     * @return string
     */
    private function _dir_location(string $name) : string
    {
        return PATH['entries'] . '/' . $name;
    }
}
