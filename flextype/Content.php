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

class Content
{
    /**
     * An instance of the Content class
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
     * Current page data array
     *
     * @var array
     * @access private
     */
    private static $page = [];

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
        Content::init();
    }

    /**
     * Init Content
     *
     * @access private
     * @return void
     */
    private static function init() : void
    {
        Content::processCurrentPage();
    }

    /**
     * Process Current Page
     *
     * @access private
     * @return void
     */
    private static function processCurrentPage() : void
    {
        // Event: The page is not processed and not sent to the display.
        Event::dispatch('onCurrentPageBeforeProcessed');

        // Init Parsers
        Content::initParsers();

        // Event: The page has been not loaded.
        Event::dispatch('onCurrentPageBeforeLoaded');

        // Set current requested page data to global $page array
        Content::$page = Content::getPage(Http::getUriString());

        // Event: The page has been fully processed and not sent to the display.
        Event::dispatch('onCurrentPageBeforeDisplayed');

        // Display page for current requested url
        Content::displayCurrentPage();

        // Event: The page has been fully processed and sent to the display.
        Event::dispatch('onCurrentPageAfterProcessed');
    }

    /**
     * Get current page
     *
     * $page = Content::getCurrentPage();
     *
     * @access  public
     * @return  array
     */
    public static function getCurrentPage() : array
    {
        return Content::$page;
    }

    /**
     * Update current page
     *
     * Content::updateCurrentPage('title', 'New page title');
     *
     * @access  public
     * @param   string $path  Array path
     * @param   mixed  $value Value to set
     * @return  void
     */
    public static function updateCurrentPage(string $path, $value) : void
    {
        Arr::set(Content::$page, $path, $value);
    }

    /**
     * Get page
     *
     * $page = Content::getPage('projects');
     *
     * @access  public
     * @param   string   $url    Page url
     * @param   bool     $raw    Raw or not raw content
     * @param   bool     $hidden Get hidden pages
     * @return  array|string
     */
    public static function getPage(string $url = '', bool $raw = false, bool $hidden = false)
    {
        // if $url is empty then set path for defined main page
        if ($url === '') {
            $file_path = PATH['pages'] . '/' . Registry::get('settings.pages.main') . '/page.html';
        } else {
            $file_path = PATH['pages'] . '/'  . $url . '/page.html';
        }

        $page_cache_id = '';

        // Page cache id
        if (Filesystem::fileExists($file_path)) {
            $page_cache_id = md5('page' . $file_path . filemtime($file_path) . (($raw === true) ? 'true' : 'false'));
        }

        // Try to get page from cache
        if (Cache::contains($page_cache_id)) {
            if (!Filesystem::fileExists($file_path)) {
                Http::setResponseStatus(404);
            }
            return Cache::fetch($page_cache_id);
        } else {

            // Get 404 page if page file is not exists
            if (!Filesystem::fileExists($file_path)) {
                if (Filesystem::fileExists($file_path = PATH['pages'] . '/404/page.html')) {
                    Http::setResponseStatus(404);
                } else {
                    throw new \RuntimeException("404 page file does not exist.");
                }
            }

            // Get raw page if $raw is true
            if ($raw) {
                $page = Content::processPage($file_path, true);
            } else {
                $page = Content::processPage($file_path);

                // Don't proccess 404 page if we want to get hidden page.
                if ($hidden === false) {
                    // Get 404 page if page is not published
                    if (isset($page['visibility']) && ($page['visibility'] === 'draft' || $page['visibility'] === 'hidden')) {
                        if (Filesystem::fileExists($file_path = PATH['pages'] . '/404/page.html')) {
                            $page = Content::processPage($file_path);
                            Http::setResponseStatus(404);
                        } else {
                            throw new \RuntimeException("404 page file does not exist.");
                        }
                    }
                }
            }

            Cache::save($page_cache_id, $page);
            return $page;
        }
    }

    /**
     * Get pages
     *
     * // Get list of subpages for page 'projects'
     * $pages = Content::getPages('projects');
     *
     * // Get list of subpages for page 'projects' and order by date and order type DESC
     * $pages = Content::getPages('projects', false, 'date', 'DESC');
     *
     * @access  public
     * @param   string  $url      Page url
     * @param   bool    $raw      Raw or not raw content
     * @param   string  $order_by Order type
     * @param   int     $offset   Offset
     * @param   int     $length   Length
     * @return  array
     */
    public static function getPages(string $url = '', bool $raw = false, string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null) : array
    {
        // if $url is empty then set path for defined main page
        if ($url === '') {
            $file_path = PATH['pages'] . '/';
        } else {
            $file_path = PATH['pages'] . '/' . $url;
        }

        // Pages array where founded pages will stored
        $pages = [];

        // Pages cache id
        $pages_cache_id = '';

        // Get pages for $url
        // If $url is empty then we want to have a list of pages for /pages dir.
        if ($url === '') {

            // Get pages list
            $pages_list = Filesystem::getFilesList($file_path, 'html');

            // Create pages cached id
            foreach ($pages_list as $key => $page) {
                $pages_cache_id .= md5('pages' . $page . filemtime($page) . (($raw === true) ? 'true' : 'false') . $order_by . $order_type . $offset . $length);
            }

            if (Cache::contains($pages_cache_id)) {
                $pages = Cache::fetch($pages_cache_id);
            } else {
                // Create pages array from pages list
                foreach ($pages_list as $key => $page) {
                    $pages[$key] = Content::processPage($page, $raw);
                }

                Cache::save($pages_cache_id, $pages);
            }
        } else {

            // Get pages list
            $pages_list = Filesystem::getFilesList($file_path, 'html');

            // Create pages cached id
            foreach ($pages_list as $key => $page) {
                if (strpos($page, $url . '/page.html') !== false) {
                    // ignore ...
                } else {
                    $pages_cache_id .= md5('pages' . $page . filemtime($page) . (($raw === true) ? 'true' : 'false') . $order_by . $order_type . $offset . $length);
                }
            }

            if (Cache::contains($pages_cache_id)) {
                $pages = Cache::fetch($pages_cache_id);
            } else {
                // Create pages array from pages list and ignore current requested page
                foreach ($pages_list as $key => $page) {
                    if (strpos($page, $url . '/page.html') !== false) {
                        // ignore ...
                    } else {
                        $pages[$key] = Content::processPage($page, $raw);
                    }
                }

                Cache::save($pages_cache_id, $pages);
            }
        }

        // Sort and Slice pages if $raw === false
        if (!$raw) {
            $pages = Arr::sort($pages, $order_by, $order_type);

            if ($offset !== null && $length !== null) {
                $pages = array_slice($pages, $offset, $length);
            }
        }

        // Return pages array
        return $pages;
    }

    /**
     * Returns $shortcode object
     *
     * @access public
     * @return object
     */
    public static function shortcode() : ShortcodeFacade
    {
        return Content::$shortcode;
    }

    /**
     * Front matter parser
     *
     * $content = Content::frontMatterParser($content);
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
     * Process page
     *
     * $page = Content::processPage(PATH['pages'] . '/home/page.html');
     *
     * @access public
     * @param  string $file_path      File path
     * @param  bool   $raw            Raw or not raw content
     * @param  bool   $ignore_content Ignore content parsing
     * @return array|string
     */
    public static function processPage(string $file_path, bool $raw = false, bool $ignore_content = false)
    {
        // Get page from file
        $page = trim(Filesystem::getFileContent($file_path));

        // Return raw page if $raw is true
        if ($raw) {
            return $page;
        } else {

            // Create $page_frontmatter and $page_content
            $page = Content::frontMatterParser($page);
            $page_frontmatter = $page['matter'];
            $page_content     = $page['body'];

            // Create empty $_page
            $_page = [];

            // Process $page_frontmatter with YAML and Shortcodes parsers
            $_page = YamlParser::decode(Content::processShortcodes($page_frontmatter));

            // Create page url item
            $url = str_replace(PATH['pages'], Http::getBaseUrl(), $file_path);
            $url = str_replace('page.html', '', $url);
            $url = str_replace('.html', '', $url);
            $url = str_replace('\\', '/', $url);
            $url = str_replace('///', '/', $url);
            $url = str_replace('//', '/', $url);
            $url = str_replace('http:/', 'http://', $url);
            $url = str_replace('https:/', 'https://', $url);
            $url = rtrim($url, '/');
            $_page['url'] = $url;

            // Create page slug item
            $url = str_replace(Http::getBaseUrl(), '', $url);
            $url = ltrim($url, '/');
            $url = rtrim($url, '/');
            $_page['slug'] = str_replace(Http::getBaseUrl(), '', $url);

            // Create page template item
            $_page['template'] = $_page['template'] ?? 'default';

            // Create page date item
            $_page['date'] = $_page['date'] ?? date(Registry::get('settings.date_format'), filemtime($file_path));

            // Create page content item with $page_content
            if ($ignore_content) {
                $_page['content'] = $page_content;
            } else {
                $_page['content'] = Content::processContent($page_content);
            }

            // Return page
            return $_page;
        }
    }

    /**
     * Process shortcodes
     *
     * $content = Content::processShortcodes($content);
     *
     * @access public
     * @param  string $content Content to parse
     * @return string
     */
    public static function processShortcodes(string $content) : string
    {
        return Content::shortcode()->process($content);
    }

    /**
     * Process content with markdown and shortcodes processors
     *
     * $content = Content::processContent($content);
     *
     * @access public
     * @param  string $content Content to parse
     * @return string
     */
    public static function processContent(string $content) : string
    {
        return Content::processShortcodes($content);
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
        Content::initShortcodes();
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
        Content::$shortcode = new ShortcodeFacade();

        // Event: Shortcodes initialized and now we can add our custom shortcodes
        Event::dispatch('onShortcodesInitialized');
    }

    /**
     * Display current page
     *
     * @access private
     * @return void
     */
    private static function displayCurrentPage() : void
    {
        Http::setRequestHeaders('Content-Type: text/html; charset='.Registry::get('settings.charset'));
        Themes::view(empty(Content::$page['template']) ? 'templates/default' : 'templates/' . Content::$page['template'])
            ->assign('page', Content::$page, true)
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
        if (is_null(Content::$instance)) {
            Content::$instance = new self;
        }

        return Content::$instance;
    }
}
