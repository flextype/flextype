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

use Flextype\Component\{Filesystem\Filesystem, View\View, Registry\Registry};
use Symfony\Component\Yaml\Yaml;

class Themes
{
    /**
     * An instance of the Themes class
     *
     * @var object
     */
    protected static $instance = null;

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
     * Init Themes
     *
     * @access protected
     * @return void
     */
    protected static function init() : void
    {
        // Theme Manifest
        $theme_manifest = [];

        // Theme cache id
        $theme_cache_id = '';

        // Get current theme
        $theme = Registry::get('site.theme');

        // Set empty theme item
        Registry::set('theme', []);

        // Create Unique Cache ID for Theme
        $theme_cache_id = md5('theme' . THEMES_PATH . $theme);

        if (Cache::driver()->contains($theme_cache_id)) {
            Registry::set('themes.'.Registry::get('site.theme'), Cache::driver()->fetch($theme_cache_id));
        } else {
            if (Filesystem::fileExists($theme_manifest_file = THEMES_PATH . '/' . $theme . '/' . $theme . '.yaml')) {
                $theme_manifest = Yaml::parseFile($theme_manifest_file);
                Registry::set('themes.'.Registry::get('site.theme'), $theme_manifest);
                Cache::driver()->save($theme_cache_id, $theme_manifest);
            }
        }
    }

    /**
     * Return the Themes instance.
     * Create it if it's not already created.
     *
     * @param  string $template  Template file
     * @param  string $variables Variables
     * @access public
     * @return object
     */
    public static function template(string $template, array $variables = [])
    {
        // Set view file
        // From current theme folder or from plugin folder
        if (Filesystem::fileExists(THEMES_PATH . '/' . Registry::get('site.theme') . '/views/' . $template . View::$view_ext)) {
            $template = THEMES_PATH . '/' . Registry::get('site.theme') . '/views/' . $template;
        } else {
            $template = PLUGINS_PATH . '/views/' . $template;
        }

        // Return template
        return new View($template, $variables);
    }

    /**
     * Return the Themes instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Themes();
    }
}
