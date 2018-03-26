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

use Arr;
use Url;
use Response;
use Symfony\Component\Yaml\Yaml;

class Pages
{
    /**
     * An instance of the Cache class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Page
     *
     * @var Page
     */
    public static $page;

    /**
     * Constructor
     *
     * @access  protected
     */
    protected function __construct()
    {
        // The page is not processed and not sent to the display.
        Events::dispatch('onPageBeforeRender');

        // Get current page
        static::$page = static::getPage(Url::getUriString());

        // Display page for current requested url
        static::renderPage(static::$page);

        // The page has been fully processed and sent to the display.
        Events::dispatch('onPageAfterRender');
    }

    /**
     * Page finder
     *
     * @param string $url
     * @param bool   $url_abs
     */
    public static function finder(string $url = '', bool $url_abs = false) : string
    {
        // If url is empty that its a homepage
        if ($url_abs) {
            if ($url) {
                $file = $url;
            } else {
                $file = PAGES_PATH . '/' . Config::get('site.pages.main') . '/' . 'index.md';
            }
        } else {
            if ($url) {
                $file = PAGES_PATH . '/' . $url . '/index.md';
            } else {
                $file = PAGES_PATH . '/' . Config::get('site.pages.main') . '/' . 'index.md';
            }
        }

        // Get 404 page if file not exists
        if (Flextype::filesystem()->exists($file)) {
            $file = $file;
        } else {
            $file = PAGES_PATH . '/404/index.md';
            Response::status(404);
        }

        return $file;
    }

    /**
     * Render page
     */
    public static function renderPage(array $page)
    {
        $template_ext  = '.php';
        $template_name = empty($page['template']) ? 'index' : $page['template'];
        $site_theme    = Config::get('site.theme');
        $template_path = THEMES_PATH . '/' . $site_theme . '/' . $template_name . $template_ext;

        if (Flextype::filesystem()->exists($template_path)) {
            include $template_path;
        } else {
            throw new RuntimeException("Template {$template_name} does not exist.");
        }
    }

    /**
     * Page page file
     */
    public static function parseFile(string $file) : array
    {
        $page = trim(file_get_contents($file));
        $page = explode('---', $page, 3);

        $frontmatter = Shortcodes::driver()->process($page[1]);
        $result_page = Yaml::parse($frontmatter);

        // Get page url
        $url = str_replace(PAGES_PATH, Url::getBase(), $file);
        $url = str_replace('index.md', '', $url);
        $url = str_replace('.md', '', $url);
        $url = str_replace('\\', '/', $url);
        $url = str_replace('///', '/', $url);
        $url = str_replace('//', '/', $url);
        $url = str_replace('http:/', 'http://', $url);
        $url = str_replace('https:/', 'https://', $url);
        $url = str_replace('/'.Config::get('site.pages.main'), '', $url);
        $url = rtrim($url, '/');
        $result_page['url'] = $url;

        // Get page slug
        $url = str_replace(Url::getBase(), '', $url);
        $url = ltrim($url, '/');
        $url = rtrim($url, '/');
        $result_page['slug'] = str_replace(Url::getBase(), '', $url);

        // Set page date
        $result_page['date'] = $result_page['date'] ?? date(Config::get('site.date_format'), filemtime($file));

        // Set page content
        $result_page['content'] = $page[2];

        // Return page
        return $result_page;
    }


    /**
     * Get page
     */
    public static function getPage(string $url = '', bool $raw = false, bool $url_abs = false)
    {
        $file = static::finder($url, $url_abs);

        if ($raw) {
            $page = trim(file_get_contents($file));
            static::$page = $page;
            Events::dispatch('onPageContentRawAfter');
        } else {
            $page = static::parseFile($file);
            static::$page = $page;
            static::$page['content'] = Filters::dispatch('content', static::parseContent(static::$page['content']));
            Events::dispatch('onPageContentAfter');
        }

        return static::$page;
    }

    /**
     * Parse Content
     *
     * @param $content Сontent to parse
     * @return string
     */
    public static function parseContent(string $content) : string
    {
        $content = Shortcodes::driver()->process($content);
        $content = Markdown::parse($content);

        return $content;
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
        if ($url == '') {

            // Get pages list
            $pages_list = Flextype::finder()->files()->name('*.md')->in(PAGES_PATH);

            // Create pages array from pages list
            foreach ($pages_list as $key => $page) {
                $pages[$key] = static::getPage($page->getPathname(), $raw, true);
            }

        } else {

            // Get pages list
            $pages_list = Flextype::finder()->files()->name('*.md')->in(PAGES_PATH . '/' . $url);

            // Create pages array from pages list and ignore current requested page
            foreach ($pages_list as $key => $page) {
                if (strpos($page->getPathname(), $url.'/index.md') !== false) {
                    // ignore ...
                } else {
                    $pages[$key] = static::getPage($page->getPathname(), $raw, true);
                }
            }

        }

        // Sort and Slice pages if $raw === false
        if (!$raw) {
            $pages = Arr::subvalSort($pages, $order_by, $order_type);

            if ($offset !== null && $length !== null) {
                $pages = array_slice($pages, $offset, $length);
            }
        }

        // Return pages array
        return $pages;
    }

    /**
     * Initialize Flextype Pages
     *
     * @access public
     * @return object
     */
    public static function init()
    {
        return !isset(self::$instance) and self::$instance = new Pages();
    }
}
