<?php namespace Rawilum;

use Arr;
use Url;
use Response;
use Shortcode;
use Symfony\Component\Yaml\Yaml;

/**
 * @package Rawilum
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://rawilum.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Pages
{
    /**
     * An instance of the Cache class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * @var Page
     */
    public static $page;

    /**
     * Constructor
     *
     * @param Rawilum $rawilum
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

    protected static function pageShortcodes() {
        // {site_url}
        $this->rawilum['shortcodes']->add('site_url', function() {
            return Url::getBase();
        });
    }

    /**
     * Page finder
     */
    public static function finder($url = '', $url_abs = false)
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
        if (Rawilum::$filesystem->exists($file)) {
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
    public static function renderPage($page)
    {
        $template_ext  = '.php';
        $template_name = empty($page['template']) ? 'index' : $page['template'];
        $site_theme    = Config::get('site.theme');
        $template_path = THEMES_PATH . '/' . $site_theme . '/' . $template_name . $template_ext;

        if (Rawilum::$filesystem->exists($template_path)) {
            include $template_path;
        } else {
            throw new RuntimeException("Template {$template_name} does not exist.");
        }
    }

    /**
     * Page parser
     */
    public static function parse($file)
    {
        $page = trim(file_get_contents($file));
        $page = explode('---', $page, 3);

        $frontmatter = Shortcodes::parse($page[1]);
        $result_page = Yaml::parse($frontmatter);
        $result_page['content'] = $page[2];

        return $result_page;
    }


    /**
     * Get page
     */
    public static function getPage($url = '', $raw = false, $url_abs = false)
    {
        $file = static::finder($url, $url_abs);

        if ($raw) {
            $page = trim(file_get_contents($file));
            static::$page = $page;
            Events::dispatch('onPageContentRawAfter');
        } else {
            $page = static::parse($file);
            static::$page = $page;
            static::$page['content'] = Filters::dispatch('content', static::parseContent(static::$page['content']));
            Events::dispatch('onPageContentAfter');
        }

        return static::$page;
    }

    public static function parseContent($content)
    {
        $content = Shortcodes::parse($content);
        $content = Markdown::parse($content);

        return $content;
    }

    /**
     * getPage
     */
    public static function getPages($url = '', $raw = false, $order_by = 'title', $order_type = 'DESC', $ignore = ['404', 'index'], $limit = null)
    {
        // Get pages list for current $url
        $pages_list = Rawilum::$finder->files()->name('*.md')->in(PAGES_PATH . '/' . $url);

        // Go trough pages list
        foreach ($pages_list as $key => $page) {
            $pages[$key] = static::getPage($page->getPathname(), $raw, true);
        }

        // Sort and Slice pages if !$raw
        if (!$raw) {
            $pages = Arr::subvalSort($pages, $order_by, $order_type);

            if ($limit != null) {
                $pages = array_slice($_pages, null, $limit);
            }
        }

        return $pages;
    }

    /**
     * Initialize Rawilum Pages
     *
     *  <code>
     *      Pages::init();
     *  </code>
     *
     * @access public
     * @return object
     */
    public static function init()
    {
        return !isset(self::$instance) and self::$instance = new Pages();
    }
}
