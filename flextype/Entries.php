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
use Flextype\Component\Registry\Registry;

class Entries
{

    /**
     * Fetch entry
     *
     * @access public
     * @param string $entry Entry
     * @return array|false The entry contents or false on failure.
     */
    public static function fetch(string $entry)
    {
        $entry_file = PATH['entries'] . '/' . $entry . '/entry.yaml';


        if (Filesystem::has($entry_file)) {

            $cache_id = md5('entry' . $entry_file . ((Filesystem::getTimestamp($entry_file) === false) ? '' : Filesystem::getTimestamp($entry_file)));

            // Try to get the entry from cache
            if (Cache::contains($cache_id)) {
                if ($entry_decoded = Cache::fetch($cache_id)) {
                    return $entry_decoded;
                } else {
                    return false;
                }
            } else {

                if ($entry_body = Filesystem::read($entry_file)) {
                    if ($entry_decoded = YamlParser::decode($entry_body)) {

                        // Create default entry items
                        $entry_decoded['date'] = $entry_decoded['date'] ?? date(Registry::get('settings.date_format'), Filesystem::getTimestamp($entry_file));
                        $entry_decoded['slug'] = $entry_decoded['slug'] ?? ltrim(rtrim($entry, '/'), '/');


                        // Apply Shortcodes for each entry fields
                        foreach ($entry_decoded as $key => $_entry_decoded) {
                            $entry_decoded[$key] = Shortcodes::process($_entry_decoded);
                        }

                        // Save to cache
                        Cache::save($cache_id, $entry_decoded);

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
    public static function fetchAll(string $entry, string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null) : array
    {
        // Entries array where founded entries will stored
        $entries = [];

        // Сache id
        $cache_id = '';

        // Entries path
        $entries_path = PATH['entries'] . '/' . $entry;

        // Get entries list
        $entries_list = Filesystem::listContents($entries_path);

        // Create entries cached id
        foreach ($entries_list as $current_entry) {
            if (strpos($current_entry['path'], $entry . '/entry.yaml') !== false) {
                // ignore ...
            } else {
                if ($current_entry['type'] == 'dir' && Filesystem::has($current_entry['path'] . '/entry.yaml')) {
                    $cache_id .= md5('entries' . $current_entry['path'] . Filesystem::getTimestamp($current_entry['path'] . '/entry.yaml'));
                }
            }
        }

        if (Cache::contains($cache_id)) {
            $entries = Cache::fetch($cache_id);
        } else {

            // Create entries array from entries list and ignore current requested entry
            foreach ($entries_list as $current_entry) {
                if (strpos($current_entry['path'], $entry . '/entry.yaml') !== false) {
                    // ignore ...
                } else {
                    if ($current_entry['type'] == 'dir' && Filesystem::has($current_entry['path'] . '/entry.yaml')) {
                        $entries[$current_entry['dirname']] = Entries::fetch($entry . '/' . $current_entry['dirname']);
                    }
                }
            }

            Cache::save($cache_id, $entries);
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
    public static function rename(string $entry, string $new_entry) : bool
    {
        return rename($entry, $new_entry);
    }

    /**
     * Update entry
     *
     * @access public
     * @param string $entry Entry
     * @param array  $data  Data
     * @return bool
     */
    public static function update(string $entry, array $data) : bool
    {
        $entry_file = PATH['entries'] . '/' . $entry . '/entry.yaml';

        if (Filesystem::has($entry_file)) {
            return Filesystem::write($entry_file, YamlParser::encode($data));
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
    public static function create(string $entry, array $data) : bool
    {
        $entry_dir = PATH['entries'] . '/' . $entry;

        // Check if new entry directory exists
        if (!Filesystem::has($entry_dir)) {

            // Try to create directory for new entry
            if (Filesystem::createDir($entry_dir)) {

                $entry_file = $entry_dir . '/entry.yaml';

                // Check if new entry file exists
                if (!Filesystem::has($entry_file)) {
                    return Filesystem::write($entry_file, YamlParser::encode($data));
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
    public static function delete(string $entry) : bool
    {
        return Filesystem::deleteDir(PATH['entries'] . '/' . $entry);
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
    public static function copy(string $entry, string $new_entry, bool $recursive = false) : bool
    {
        return Filesystem::copy($entry, $new_entry, $recursive);
    }

    /**
     * Check whether entry exists.
     *
     * @access public
     * @param string $entry Entry
     * @return bool
     */
    public static function has(string $entry) : bool
    {
        return Filesystem::has(PATH['entries'] . '/' . $entry . '/entry.yaml');
    }
}
