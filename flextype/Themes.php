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
    private static $instance = null;

    /**
     * Private clone method to enforce singleton behavior.
     *
     * @access private
     */
    private function __clone() { }

    /**
     * Private wakeup method to enforce singleton behavior.
     *
     * @access private
     */
    private function __wakeup() { }

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    private function __construct()
    {
        Themes::init();
    }

    /**
     * Init Themes
     *
     * @access private
     * @return void
     */
    private static function init() : void
    {
        // Theme Manifest
        $theme_manifest = [];

        // Theme cache id
        $theme_cache_id = '';

        // Get current theme
        $theme = Registry::get('site.theme');

        // Set empty themes items
        Registry::set('themes', []);

        // Create Unique Cache ID for Theme
        $theme_cache_id = md5('theme' . PATH['themes'] . $theme);

        // Get Theme mafifest file and write to site.themes array
        if (Cache::contains($theme_cache_id)) {
            Registry::set('themes.'.Registry::get('site.theme'), Cache::fetch($theme_cache_id));
        } else {
            if (Filesystem::fileExists($theme_manifest_file = PATH['themes'] . '/' . $theme . '/' . $theme . '.yaml')) {
                $theme_manifest = Yaml::parseFile($theme_manifest_file);
                Registry::set('themes.'.Registry::get('site.theme'), $theme_manifest);
                Cache::save($theme_cache_id, $theme_manifest);
            }
        }
    }

    /**
     * Get themes view
     *
     * @param  string $template  Template file
     * @param  string $variables Variables
     * @access public
     * @return object
     */
    public static function view(string $template, array $variables = [])
    {
        // Set view file
        // From current theme folder or from plugin folder
        if (Filesystem::fileExists(PATH['themes'] . '/' . Registry::get('site.theme') . '/views/' . $template . View::$view_ext)) {
            $template = PATH['themes'] . '/' . Registry::get('site.theme') . '/views/' . $template;
        } else {
            $template = PATH['plugins'] . '/' . $template;
        }

        // Return template
        return new View($template, $variables);
    }

    /**
     * Get the Themes instance.
     *
     * @access public
     * @return object
     */
     public static function getInstance()
     {
        if (is_null(Themes::$instance)) {
            Themes::$instance = new self;
        }

        return Themes::$instance;
     }
}
