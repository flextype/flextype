<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Http\Http;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Event\Event;
use Flextype\Component\Registry\Registry;

class Entries
{
    /**
     * Get entry
     *
     * $entry = Entries::getEntry('projects');
     *
     * @access  public
     * @param   string   $entry  Entry
     * @param   bool     $raw    Parse content or raw content without parsing.
     * @param   bool     $hidden Get hidden entries.
     * @return  array
     */
    public static function getEntry(string $entry = '', bool $raw = false, bool $hidden = false) : array
    {
        // Init entry data
        $data = [];

        // If $url is empty then set path for default main entry
        if ($entry === '') {
            $file_path = PATH['entries'] . '/' . Registry::get('settings.entries.main') . '/entry.html';
        } else {
            $file_path = PATH['entries'] . '/' . $entry . '/entry.html';
        }

        // If entry exist
        if (Filesystem::has($file_path)) {
            $entry_cache_id = md5('entry' . $file_path . filemtime($file_path) . (($raw === true) ? 'true' : 'false') . (($hidden === true) ? 'true' : 'false'));

            // Try to get the entry from cache
            if (Cache::contains($entry_cache_id)) {
                if ($_entry = Cache::fetch($entry_cache_id)) {
                    $entry = $_entry;
                }
            } else {

                // Get raw entry if $raw is true
                if ($raw) {
                    $data = Entries::processEntry($file_path, true);
                } else {
                    $data = Entries::processEntry($file_path);

                    // Don't proccess 404 entry if we want to get hidden entry.
                    if ($hidden === false) {

                        // Get 404 entry if entry is not published
                        if (isset($entry['visibility']) && ($entry['visibility'] === 'draft' || $entry['visibility'] === 'hidden')) {
                            $data = Entries::getError404Entry();
                        }
                    }
                }

                Cache::save($entry_cache_id, $data);
            }
        } else {
            $data = Entries::getError404Entry();
        }

        return $data;
    }

    /**
     * Get entries
     *
     * // Get list of subentries for entry 'projects'
     * $entries = Entries::getEntries('projects');
     *
     * // Get list of subentries for entry 'projects' and order by date and order type DESC
     * $entries = Entries::getEntries('projects', false, 'date', 'DESC');
     *
     * @access  public
     * @param   string  $url        Page url.
     * @param   string  $order_by   Order by specific entry field.
     * @param   string  $order_type Order type: DESC or ASC
     * @param   int     $offset     Offset
     * @param   int     $length     Length
     * @param   bool    $multilevel Get nested entries or not.
     * @param   bool    $raw        Parse content or raw content without parsing.
     * @return  array
     */
    public static function getEntries(string $url = '', string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null, bool $multilevel = false, bool $raw = false) : array
    {
        // if $url is empty then set path for defined main entry
        if ($url === '') {
            $file_path = PATH['entries'] . '/';
        } else {
            $file_path = PATH['entries'] . '/' . $url;
        }

        // Pages array where founded entries will stored
        $entries = [];

        // Pages cache id
        $entry_cache_id = '';

        // Get entries for $url
        // If $url is empty then we want to have a list of entries for /entries dir.
        if ($url === '') {

            // Get entries list
            $entries_list = Filesystem::listContents($file_path);

            // Create entries cached id
            foreach ($entries_list as $entry) {
                if ($entry['type'] == 'dir' && Filesystem::has($entry['path'] . '/entry.html')) {
                    $entry_cache_id .= md5('entries' . $entry['path'] . $entry['timestamp'] . (($raw === true) ? 'true' : 'false') . (($multilevel === true) ? 'true' : 'false') . $order_by . $order_type . $offset . $length);
                }
            }

            if (Cache::contains($entry_cache_id)) {
                $entries = Cache::fetch($entry_cache_id);
            } else {
                // Create entries array from entries list
                foreach ($entries_list as $entry) {
                    if ($entry['type'] == 'dir' && Filesystem::has($entry['path'] . '/entry.html')) {
                        $entries[$entry['dirname']] = Entries::processEntry($entry['path'] . '/entry.html', $raw);
                    }
                }

                Cache::save($entry_cache_id, $entries);
            }
        } else {

            // Get entries list
            $entries_list = Filesystem::listContents($file_path);

            // Create entries cached id
            foreach ($entries_list as $entry) {
                if (strpos($entry['path'], $url . '/entry.html') !== false) {
                    // ignore ...
                } else {
                    if ($entry['type'] == 'dir' && Filesystem::has($entry['path'] . '/entry.html')) {
                        $entry_cache_id .= md5('entries' . $entry['path'] . $entry['timestamp'] . (($raw === true) ? 'true' : 'false') . $order_by . $order_type . $offset . $length);
                    }
                }
            }

            if (Cache::contains($entry_cache_id)) {
                $entries = Cache::fetch($entry_cache_id);
            } else {
                // Create entries array from entries list and ignore current requested entry
                foreach ($entries_list as $entry) {
                    if (strpos($entry['path'], $url . '/entry.html') !== false) {
                        // ignore ...
                    } else {
                        if ($entry['type'] == 'dir' && Filesystem::has($entry['path'] . '/entry.html')) {
                            $entries[$entry['dirname']] = Entries::processEntry($entry['path'] . '/entry.html', $raw);
                        }
                    }
                }

                Cache::save($entry_cache_id, $entries);
            }
        }

        // Sort and Slice entries if $raw === false
        if (count($entries) > 0) {
            if (!$raw) {
                $entries = Arr::sort($entries, $order_by, $order_type);

                if ($offset !== null && $length !== null) {
                    $entries = array_slice($entries, $offset, $length);
                }
            }
        }

        // Return entries array
        return $entries;
    }

    /**
     * Get Error404 entry
     *
     * @return  array
     */
    private static function getError404Entry() : array
    {
        Http::setResponseStatus(404);

        $entry = [];

        $entry['title']       = Registry::get('settings.entries.error404.title');
        $entry['description'] = Registry::get('settings.entries.error404.description');
        $entry['content']     = Registry::get('settings.entries.error404.content');
        $entry['template']    = Registry::get('settings.entries.error404.template');

        return $entry;
    }

    /**
     * Process entry
     *
     * $entry = Entries::processEntry(PATH['entries'] . '/home/entry.html');
     *
     * @access public
     * @param  string $file_path      File path
     * @param  bool   $raw            Raw or not raw content
     * @param  bool   $ignore_content Ignore content parsing
     * @return array|string
     */
    public static function processEntry(string $file_path, bool $raw = false, bool $ignore_content = false)
    {
        // Init Entry
        $entry = '';

        // Get entry from file
        if ($entry_content = Filesystem::read($file_path)) {
            $entry = $entry_content;
        }

        $entry = trim($entry);

        // Return raw entry if $raw is true
        if ($raw) {
            return $entry;
        } else {

            // Create $entry_frontmatter and $entry_content
            $entry = FrontmatterParser::parse($entry);
            $entry_frontmatter = $entry['matter'];
            $entry_content     = $entry['body'];

            // Process $entry_frontmatter with YAML and Shortcodes parsers
            $_entry = YamlParser::decode(Shortcodes::process($entry_frontmatter));

            // Create entry url item
            $url = str_replace(PATH['entries'], Http::getBaseUrl(), $file_path);
            $url = str_replace('entry.html', '', $url);
            $url = str_replace('.html', '', $url);
            $url = str_replace('\\', '/', $url);
            $url = str_replace('///', '/', $url);
            $url = str_replace('//', '/', $url);
            $url = str_replace('http:/', 'http://', $url);
            $url = str_replace('https:/', 'https://', $url);
            $url = rtrim($url, '/');
            $_entry['url'] = $url;

            // Create entry slug item
            $url = str_replace(Http::getBaseUrl(), '', $url);
            $url = ltrim($url, '/');
            $url = rtrim($url, '/');
            $_entry['slug'] = str_replace(Http::getBaseUrl(), '', $url);

            // Create entry base url
            $_entry['base_url'] = Http::getBaseUrl() . '/site/entries/';

            // Create entry template item
            $_entry['template'] = $_entry['template'] ?? 'default';

            // Create entry date item
            $_entry['date'] = $_entry['date'] ?? date(Registry::get('settings.date_format'), filemtime($file_path));

            // Create entry content item with $entry_content
            if ($ignore_content) {
                $_entry['content'] = $entry_content;
            } else {
                $_entry['content'] = Shortcodes::process($entry_content);
            }

            // Return entry
            return $_entry;
        }
    }

    /**
     * Fetch entry
     *
     * @param string $entry Entry
     * @return string|false The entry contents or false on failure.
     */
    public static function fetch(string $entry)
    {
        $entry_file = PATH['entries'] . '/' . $entry . '/entry.html';

        if (Filesystem::has($entry_file)) {
            return YamlParser::decode(Filesystem::read($entry_file));
        } else {
            return false;
        }
    }

    /**
     * Fetch entry
     *
     * @param string $entry Entry
     * @return string|false The entry contents or false on failure.
     */
    public static function fetchAll(string $entry)
    {
        $entry_file = PATH['entries'] . '/' . $entry . '/entry.html';

        if (Filesystem::has($entry_file)) {
            return YamlParser::decode(Filesystem::read($entry_file));
        } else {
            return false;
        }
    }

    /**
     * Rename entry.
     *
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
     * @param string $entry Entry
     * @param array  $data  Data
     * @return bool
     */
    public static function update(string $entry, array $data) : bool
    {
        $entry_file = PATH['entries'] . '/' . $entry . '/entry.html';

        if (Filesystem::has($entry_file)) {
            return Filesystem::write($entry_file, YamlParser::encode($data));
        } else {
            return false;
        }
    }

    /**
     * Create entry
     *
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

                $entry_file = $entry_dir . '/entry.html';

                // Check if new entry file exists
                if (!Filesystem::has($entry_file)) {
                    return Filesystem::write($entry_file, YamlParser::encode($data));
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
     * @param string $entry Entry
     * @return bool True on success, false on failure.
     */
    public static function delete(string $entry) : bool
    {
        return Filesystem::delete(PATH['entries'] . '/' . $entry);
    }

    /**
     * Copy entry(s)
     *
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
     * @param string $entry Entry
     * @return bool
     */
    public static function has(string $entry) : bool
    {
        return Filesystem::has(PATH['entries'] . '/' . $entry . '/entry.html');
    }
}
