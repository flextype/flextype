<?php namespace Rawilum;

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

class Themes
{
    /**
     * An instance of the Themes class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Init Themes
     *
     * @access public
     * @return mixed
     */
    protected function __construct()
    {
        // Theme Manifest
        $theme_manifest = [];

        // Get current theme
        $theme = Config::get('site.theme');

        if (Rawilum::$filesystem->exists($theme_manifest_file = THEMES_PATH . '/' . $theme . '/' . $theme . '.yml')) {
            $theme_manifest = Yaml::parseFile($theme_manifest_file);
            Config::set('themes.'.Config::get('site.theme'), $theme_manifest);
        }
    }

    /**
     * Initialize Rawilum Themes
     *
     *  <code>
     *      Themes::init();
     *  </code>
     *
     * @access public
     * @return object
     */
    public static function init()
    {
        return !isset(self::$instance) and self::$instance = new Themes();
    }
}
