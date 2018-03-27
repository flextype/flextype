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
        $theme = Config::get('site.theme');

        // Create Unique Cache ID for Theme
        $theme_cache_id = md5('theme' . THEMES_PATH . $theme);

        if (Cache::driver()->contains($theme_cache_id)) {
            Config::set('themes.'.Config::get('site.theme'), Cache::driver()->fetch($theme_cache_id));
        } else {
            if (Flextype::filesystem()->exists($theme_manifest_file = THEMES_PATH . '/' . $theme . '/' . $theme . '.yml')) {
                $theme_manifest = Yaml::parseFile($theme_manifest_file);
                Config::set('themes.'.Config::get('site.theme'), $theme_manifest);
                Cache::driver()->save($theme_cache_id, $theme_manifest);
            }
        }
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
