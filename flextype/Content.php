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
        static::init();
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

        // Create Markdown Parser object
        Content::$markdown = new Markdown();

        // Create Shortcode Parser object
        Content::$shortcode = new ShortcodeFacade();

        // Register default shortcodes
        Content::registerDefaultShortcodes();

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
     */
    public static function getPage(string $url = '', bool $raw = false) : array
    {
        // if $url is empty then set path for defined main page
        if ($url === '') {
            $file_path = PATH['pages'] . '/' . Registry::get('site.pages.main') . '/page.md';
        } else {
            $file_path = PATH['pages'] . '/'  . $url . '/page.md';
        }

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

        return Content::$page;
    }

    /**
     * Get Pages
     */
    public static function getPages(string $url = '', bool $raw = false, string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null)
    {
        // Pages array where founded pages will stored
        $pages = [];

        // Get pages for $url
        // If $url is empty then we want to have a list of pages for /pages dir.
        if ($url === '') {

            // Get pages list
            $pages_list = Filesystem::getFilesList(PATH['pages'] . '/' , 'md');

            // Create pages array from pages list
            foreach ($pages_list as $key => $page) {
                $pages[$key] = Content::processPage($page, $raw);
            }

        } else {

            // Get pages list
            $pages_list = Filesystem::getFilesList(PATH['pages'] . '/' . $url, 'md');

            // Create pages array from pages list and ignore current requested page
            foreach ($pages_list as $key => $page) {
                if (strpos($page, $url . '/page.md') !== false) {
                    // ignore ...
                } else {
                    $pages[$key] = Content::processPage($page, $raw);
                }
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

    public static function processPage(string $file, bool $raw = false)
    {
        // Get page from file
        $page = trim(Filesystem::getFileContent($file));

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
            $_page = Yaml::parse(Content::processContentShortcodes($page_frontmatter));

            // Create page url item
            $url = str_replace(PATH['pages'] , Http::getBaseUrl(), $file);
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
            $_page['date'] = $result_page['date'] ?? date(Registry::get('site.date_format'), filemtime($file));

            // Create page content item with $page_content
            $_page['content'] = Content::processContent($page_content);

            // Return page
            return $_page;
        }
    }

    public static function processContentShortcodes(string $content) : string
    {
        return Content::shortcode()->process($content);
    }

    public static function processContentMarkdown(string $content) : string
    {
        return Content::$markdown->text($content);
    }

    public static function processContent(string $content) : string
    {
        $content = Content::processContentShortcodes($content);
        $content = Content::processContentMarkdown($content);

        return $content;
    }

    /**
     * Register default shortcodes
     *
     * @access protected
     */
    protected static function registerDefaultShortcodes() : void
    {
        Content::shortcode()->addHandler('site_url', function() {
            return Http::getBaseUrl();
        });

        Content::shortcode()->addHandler('block', function(ShortcodeInterface $s) {
            return $s->getParameter('name');
        });
    }

    /**
     * Display current page
     *
     * @access protected
     * @return void
     */
    protected static function displayCurrentPage() : void
    {
        Themes::template(empty(Content::$page['template']) ? 'templates/default' : 'templates/' . Content::$page['template'])
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
