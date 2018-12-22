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
use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

class Entry
{
    /**
     * An instance of the Entry class
     *
     * @var object
     * @access private
     */
    private static $instance = null;

    /**
     * Shortcode object
     *
     * @var object
     * @access private
     */
    private static $shortcode = null;

    /**
     * Current entry data array
     *
     * @var array
     * @access private
     */
    private static $entry = [];

    /**
     * Private clone method to enforce singleton behavior.
     *
     * @access private
     */
    private function __clone()
    {
    }

    /**
     * Private wakeup method to enforce singleton behavior.
     *
     * @access private
     */
    private function __wakeup()
    {
    }

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    private function __construct()
    {
        Entry::init();
    }

    /**
     * Init Entry
     *
     * @access private
     * @return void
     */
    private static function init() : void
    {
        Entry::processCurrentEntry();
    }

    /**
     * Process Current Entry
     *
     * @access private
     * @return void
     */
    private static function processCurrentEntry() : void
    {
        // Event: The entry is not processed and not sent to the display.
        Event::dispatch('onCurrentEntryBeforeProcessed');

        // Init Parsers
        Entry::initParsers();

        // Event: The entry has been not loaded.
        Event::dispatch('onCurrentEntryBeforeLoaded');

        // Set current requested entry data to global $entry array
        Entry::$entry = Entry::getEntry(Http::getUriString());

        // Event: The entry has been fully processed and not sent to the display.
        Event::dispatch('onCurrentEntryBeforeDisplayed');

        // Display entry for current requested url
        Entry::displayCurrentEntry();

        // Event: The entry has been fully processed and sent to the display.
        Event::dispatch('onCurrentEntryAfterProcessed');
    }

    /**
     * Get current entry
     *
     * $entry = Entry::getCurrentPage();
     *
     * @access  public
     * @return  array
     */
    public static function getCurrentEntry() : array
    {
        return Entry::$entry;
    }

    /**
     * Update current entry
     *
     * Entry::updateCurrentPage('title', 'New entry title');
     *
     * @access  public
     * @param   string $path  Array path
     * @param   mixed  $value Value to set
     * @return  void
     */
    public static function updateCurrentEntry(string $path, $value) : void
    {
        Arr::set(Entry::$entry, $path, $value);
    }

    /**
     * Get entry
     *
     * $entry = Entry::getEntry('projects');
     *
     * @access  public
     * @param   string   $url    Page url
     * @param   bool     $raw    Raw or not raw content
     * @param   bool     $hidden Get hidden entries
     * @return  array|string
     */
    public static function getEntry(string $url = '', bool $raw = false, bool $hidden = false)
    {
        // If $url is empty then set path for default main entry
        if ($url === '') {
            $file_path = PATH['entries'] . '/' . Registry::get('settings.entries.main') . '/entry.html';
        } else {
            $file_path = PATH['entries'] . '/'  . $url . '/entry.html';
        }

        // If entry exist
        if (Filesystem::fileExists($file_path)) {
            $entry_cache_id = md5('entry' . $file_path . filemtime($file_path) . (($raw === true) ? 'true' : 'false') . (($hidden === true) ? 'true' : 'false'));

            // Try to get the entry from cache
            if (Cache::contains($entry_cache_id)) {
                return Cache::fetch($entry_cache_id);
            } else {

                // Get raw entry if $raw is true
                if ($raw) {
                    $entry = Entry::processEntry($file_path, true);
                } else {
                    $entry = Entry::processEntry($file_path);

                    // Don't proccess 404 entry if we want to get hidden entry.
                    if ($hidden === false) {

                        // Get 404 entry if entry is not published
                        if (isset($entry['visibility']) && ($entry['visibility'] === 'draft' || $entry['visibility'] === 'hidden')) {
                            $entry = Entry::getError404Entry();
                        }
                    }
                }

                Cache::save($entry_cache_id, $entry);
                return $entry;
            }
        } else {
            return Entry::getError404Entry();
        }
    }

    private static function getError404Entry() : array
    {
        Http::setResponseStatus(404);

        $entry['title']       = Registry::get('settings.entries.error404.title');
        $entry['description'] = Registry::get('settings.entries.error404.description');
        $entry['content']     = Registry::get('settings.entries.error404.content');
        $entry['template']    = Registry::get('settings.entries.error404.template');

        return $entry;
    }

    /**
     * Get entries
     *
     * // Get list of subentries for entry 'projects'
     * $entries = Entry::getEntries('projects');
     *
     * // Get list of subentries for entry 'projects' and order by date and order type DESC
     * $entries = Entry::getEntries('projects', false, 'date', 'DESC');
     *
     * @access  public
     * @param   string  $url      Page url
     * @param   bool    $raw      Raw or not raw content
     * @param   string  $order_by Order type
     * @param   int     $offset   Offset
     * @param   int     $length   Length
     * @return  array
     */
    public static function getEntries(string $url = '', bool $raw = false, string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null, bool $multilevel = true) : array
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
            $entries_list = Filesystem::getFilesList($file_path, 'html', true, $multilevel);

            // Create entries cached id
            foreach ($entries_list as $key => $entry) {
                $entry_cache_id .= md5('entries' . $entry . filemtime($entry) . (($raw === true) ? 'true' : 'false') . $order_by . $order_type . $offset . $length);
            }

            if (Cache::contains($entry_cache_id)) {
                $entries = Cache::fetch($entry_cache_id);
            } else {
                // Create entries array from entries list
                foreach ($entries_list as $key => $entry) {
                    $entries[$key] = Entry::processEntry($entry, $raw);
                }

                Cache::save($entry_cache_id, $entries);
            }
        } else {

            // Get entries list
            $entries_list = Filesystem::getFilesList($file_path, 'html', true, $multilevel);

            // Create entries cached id
            foreach ($entries_list as $key => $entry) {
                if (strpos($entry, $url . '/entry.html') !== false) {
                    // ignore ...
                } else {
                    $entry_cache_id .= md5('entries' . $entry . filemtime($entry) . (($raw === true) ? 'true' : 'false') . $order_by . $order_type . $offset . $length);
                }
            }

            if (Cache::contains($entry_cache_id)) {
                $entries = Cache::fetch($entry_cache_id);
            } else {
                // Create entries array from entries list and ignore current requested entry
                foreach ($entries_list as $key => $entry) {
                    if (strpos($entry, $url . '/entry.html') !== false) {
                        // ignore ...
                    } else {
                        $entries[$key] = Entry::processEntry($entry, $raw);
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
     * Returns $shortcode object
     *
     * @access public
     * @return object
     */
    public static function shortcode() : ShortcodeFacade
    {
        return Entry::$shortcode;
    }

    /**
     * Front matter parser
     *
     * $content = Entry::frontMatterParser($content);
     *
     * @param  string $content Content to parse
     * @access public
     * @return array
     */
    public static function frontMatterParser(string $content) : array
    {
       $parts = preg_split('/^[\s\r\n]?---[\s\r\n]?$/sm', PHP_EOL.ltrim($content));

       if (count($parts) < 3) return ['matter' => [], 'body' => $content];

       return ['matter' => trim($parts[1]), 'body' => implode(PHP_EOL.'---'.PHP_EOL, array_slice($parts, 2))];
    }

    /**
     * Process entry
     *
     * $entry = Entry::processEntry(PATH['entries'] . '/home/entry.html');
     *
     * @access public
     * @param  string $file_path      File path
     * @param  bool   $raw            Raw or not raw content
     * @param  bool   $ignore_content Ignore content parsing
     * @return array|string
     */
    public static function processEntry(string $file_path, bool $raw = false, bool $ignore_content = false)
    {
        // Get entry from file
        $entry = trim(Filesystem::getFileContent($file_path));

        // Return raw entry if $raw is true
        if ($raw) {
            return $entry;
        } else {

            // Create $entry_frontmatter and $entry_content
            $entry = Entry::frontMatterParser($entry);
            $entry_frontmatter = $entry['matter'];
            $entry_content     = $entry['body'];

            // Create empty $_entry
            $_entry = [];

            // Process $entry_frontmatter with YAML and Shortcodes parsers
            $_entry = YamlParser::decode(Entry::processShortcodes($entry_frontmatter));

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

            // Create entry template item
            $_entry['template'] = $_entry['template'] ?? 'default';

            // Create entry date item
            $_entry['date'] = $_entry['date'] ?? date(Registry::get('settings.date_format'), filemtime($file_path));

            // Create entry content item with $entry_content
            if ($ignore_content) {
                $_entry['content'] = $entry_content;
            } else {
                $_entry['content'] = Entry::processContent($entry_content);
            }

            // Return entry
            return $_entry;
        }
    }

    /**
     * Process shortcodes
     *
     * $content = Entry::processShortcodes($content);
     *
     * @access public
     * @param  string $content Content to parse
     * @return string
     */
    public static function processShortcodes(string $content) : string
    {
        return Entry::shortcode()->process($content);
    }

    /**
     * Process content with markdown and shortcodes processors
     *
     * $content = Entry::processContent($content);
     *
     * @access public
     * @param  string $content Content to parse
     * @return string
     */
    public static function processContent(string $content) : string
    {
        return Entry::processShortcodes($content);
    }

    /**
     * Init Parsers
     *
     * @access private
     * @return void
     */
    private static function initParsers() : void
    {
        // Init Shortcodes
        Entry::initShortcodes();
    }

    /**
     * Init Shortcodes
     *
     * @access private
     * @return void
     */
    private static function initShortcodes() : void
    {
        // Create Shortcode Parser object
        Entry::$shortcode = new ShortcodeFacade();

        // Event: Shortcodes initialized and now we can add our custom shortcodes
        Event::dispatch('onShortcodesInitialized');
    }

    /**
     * Display current entry
     *
     * @access private
     * @return void
     */
    private static function displayCurrentEntry() : void
    {
        Http::setRequestHeaders('Content-Type: text/html; charset='.Registry::get('settings.charset'));
        Themes::view(empty(Entry::$entry['template']) ? 'templates/default' : 'templates/' . Entry::$entry['template'])
            ->assign('entry', Entry::$entry, true)
            ->display();
    }

    /**
     * Get the Content instance.
     *
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (is_null(Entry::$instance)) {
            Entry::$instance = new self;
        }

        return Entry::$instance;
    }
}
