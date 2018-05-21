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

use Flextype\Component\{Arr\Arr, Http\Http, Filesystem\Filesystem, Event\Event, Registry\Registry};
use Symfony\Component\Yaml\Yaml;
use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use ParsedownExtra as Markdown;

class Content
{
    /**
     * An instance of the Cache class
     *
     * @var object
     * @access protected
     */
    protected static $instance = null;

    /**
     * Markdown Object
     *
     * @var object
     * @access private
     */
    private static $markdown = null;

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
     * @access protected
     */
    private static $page = [];

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        Content::init();
    }

    /**
     * Init Pages
     *
     * @access protected
     * @return void
     */
    protected static function init() : void
    {
        // Event: The page is not processed and not sent to the display.
        Event::dispatch('onPageBeforeRender');

        // Init Markdown
        Content::initMarkdown();

        // Init Shortcodes
        Content::initShortcodes();

        // Set current requested page data to $page array
        Content::$page = Content::getPage(Http::getUriString());

        // Display page for current requested url
        Content::displayCurrentPage();

        // Event: The page has been fully processed and sent to the display.
        Event::dispatch('onPageAfterRender');
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
     * @param   string   $path   Array path
     * @param   mixed    $value  Value to set
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
     * @param   string   $url  Page url
     * @param   bool     $raw  Raw or not raw content
     * @return  array|string
     */
    public static function getPage(string $url = '', bool $raw = false)
    {
        // if $url is empty then set path for defined main page
        if ($url === '') {
            $file_path = PATH['pages'] . '/' . Registry::get('site.pages.main') . '/page.md';
        } else {
            $file_path = PATH['pages'] . '/'  . $url . '/page.md';
        }

        $page_cache_id = '';

        // Page cache id
        if (Filesystem::fileExists($file_path)) {
            $page_cache_id = md5('page' . $file_path . filemtime($file_path) . (($raw === true) ? 'true' : 'false'));
        }

        // Try to get page from cache
        if (Cache::contains($page_cache_id)) {
            return Cache::fetch($page_cache_id);
        } else {

            // Get 404 page if page file is not exists
            if (Filesystem::fileExists($file_path)) {
                $file_path = $file_path;
            } else {
                if (Filesystem::fileExists($file_path = PATH['pages'] . '/404/page.md')) {
                    $file_path = $file_path;
                    Http::setResponseStatus(404);
                } else {
                    throw new \RuntimeException("404 page file does not exist.");
                }
            }

            // Get raw page if $raw is true
            if ($raw) {
                Content::$page = Content::processPage($file_path, true);
                Event::dispatch('onPageContentRawAfter');
            } else {
                Content::$page = Content::processPage($file_path);
                Event::dispatch('onPageContentAfter');

                // Get 404 page if page is not published
                if (isset(Content::$page['published']) && Content::$page['published'] === false) {
                    if (Filesystem::fileExists($file_path = PATH['pages'] . '/404/page.md')) {
                        Content::$page = Content::processPage($file_path);
                        Http::setResponseStatus(404);
                    } else {
                        throw new \RuntimeException("404 page file does not exist.");
                    }
                }
            }

            Cache::save($page_cache_id, Content::$page);
            return Content::$page;
        }
    }

    /**
     * Get pages
     *
     * $pages = Content::getPages('projects');
     *
     * @access  public
     * @param   string  $url  Page url
     * @param   bool    $raw  Raw or not raw content
     * @param   string  $order_by  Order type
     * @param   int     $offset  Offset
     * @param   int     $length  Length
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
            $pages_list = Filesystem::getFilesList($file_path , 'md');

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
            $pages_list = Filesystem::getFilesList($file_path, 'md');

            // Create pages cached id
            foreach ($pages_list as $key => $page) {
                if (strpos($page, $url . '/page.md') !== false) {
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
                    if (strpos($page, $url . '/page.md') !== false) {
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
     * Get block
     *
     * $block = Content::getBlock('block-name');
     *
     * @access public
     * @param  string  $block_name  Block name
     * @param  bool    $raw  Raw or not raw content
     * @return string
     */
    public static function getBlock($block_name, $raw = false) : string
    {
        $block_path = PATH['blocks'] . '/' . $block_name . '.md';

        // Block cache id
        $block_cache_id = '';

        if (Filesystem::fileExists($block_path)) {
            $block_cache_id = md5('block' . $block_path . filemtime($block_path) . (($raw === true) ? 'true' : 'false'));
        }

        // Try to get block from cache
        if (Cache::contains($block_cache_id)) {
            return Cache::fetch($block_cache_id);
        } else {
            if (Filesystem::fileExists($block_path)) {

                $content = Filesystem::getFileContent($block_path);

                if ($raw === false) {
                    $content = Content::processContent($content);
                }

                Cache::save($block_cache_id, $content);
                return $content;
            } else {
                throw new \RuntimeException("Block does not exist.");
            }
        }
    }

    /**
     * Returns $markdown object
     *
     * @access public
     * @return object
     */
    public static function markdown() : Markdown
    {
        return Content::$markdown;
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
     * Process page
     *
     * $page = Content::processPage(PATH['pages'] . '/home/page.md');
     *
     * @access public
     * @param  string $file_path File path
     * @param  string $raw       Raw or not raw content
     * @return array|string
     */
    public static function processPage(string $file_path, bool $raw = false)
    {
        // Get page from file
        $page = trim(Filesystem::getFileContent($file_path));

        // Return raw page if $raw is true
        if ($raw) {
            return $page;
        } else {

            // Create $page_frontmatter and $page_content
            $page = explode('---', $page, 3);
            $page_frontmatter = $page[1];
            $page_content     = $page[2];

            // Create empty $_page
            $_page = [];

            // Process $page_frontmatter with YAML and Shortcodes parsers
            $_page = Yaml::parse(Content::processShortcodes($page_frontmatter));

            // Create page url item
            $url = str_replace(PATH['pages'] , Http::getBaseUrl(), $file_path);
            $url = str_replace('page.md', '', $url);
            $url = str_replace('.md', '', $url);
            $url = str_replace('\\', '/', $url);
            $url = str_replace('///', '/', $url);
            $url = str_replace('//', '/', $url);
            $url = str_replace('http:/', 'http://', $url);
            $url = str_replace('https:/', 'https://', $url);
            $url = str_replace('/'.Registry::get('site.pages.main'), '', $url);
            $url = rtrim($url, '/');
            $_page['url'] = $url;

            // Create page slug item
            $url = str_replace(Http::getBaseUrl(), '', $url);
            $url = ltrim($url, '/');
            $url = rtrim($url, '/');
            $_page['slug'] = str_replace(Http::getBaseUrl(), '', $url);

            // Create page date item
            $_page['date'] = $_page['date'] ?? date(Registry::get('site.date_format'), filemtime($file_path));

            // Create page content item with $page_content
            $_page['content'] = Content::processContent($page_content);

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
     * Process markdown
     *
     * $content = Content::processMarkdown($content);
     *
     * @access public
     * @param  string $content Content to parse
     * @return string
     */
    public static function processMarkdown(string $content) : string
    {
        return Content::$markdown->text($content);
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
        $content = Content::processShortcodes($content);
        $content = Content::processMarkdown($content);

        return $content;
    }

    /**
     * Register default shortcodes
     *
     * @access protected
     * @return void
     */
    protected static function registerDefaultShortcodes() : void
    {
        Content::shortcode()->addHandler('site_url', function() {
            return Http::getBaseUrl();
        });

        Content::shortcode()->addHandler('block', function(ShortcodeInterface $s) {
            return Content::getBlock($s->getParameter('name'), (($s->getParameter('raw') === 'true') ? true : false));
        });

        Content::shortcode()->addHandler('registry', function(ShortcodeInterface $s) {
            return Registry::get($s->getParameter('item'));
        });
    }

    /**
     * Init Markdown
     *
     * @access protected
     * @return void
     */
    protected static function initMarkdown() : void
    {
        // Create Markdown Parser object
        Content::$markdown = new Markdown();

        // Event: Markdown initialized
        Event::dispatch('onMarkdownInitialized');
    }

    /**
     * Init Shortcodes
     *
     * @access protected
     * @return void
     */
    protected static function initShortcodes() : void
    {
        // Create Shortcode Parser object
        Content::$shortcode = new ShortcodeFacade();

        // Register default shortcodes
        Content::registerDefaultShortcodes();

        // Event: Shortcodes initialized and now we can add our custom shortcodes
        Event::dispatch('onShortcodesInitialized');
    }

    /**
     * Display current page
     *
     * @access protected
     * @return void
     */
    protected static function displayCurrentPage() : void
    {
        Themes::view(empty(Content::$page['template']) ? 'templates/default' : 'templates/' . Content::$page['template'])
            ->assign('page', Content::$page, true)
            ->display();
    }

    /**
     * Return the Content instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Content();
    }
}
