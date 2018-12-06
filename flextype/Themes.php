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

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\View\View;
use Flextype\Component\Registry\Registry;
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
        $theme = Registry::get('system.theme');

        // Set empty themes items
        Registry::set('themes', []);

        // Create Unique Cache ID for Theme
        $theme_cache_id = md5('theme' . filemtime(PATH['themes'] .'/'. $theme . '/' . 'settings.yaml') .
                                        filemtime(PATH['themes'] .'/'. $theme . '/' . $theme . '.yaml'));

        // Get Theme mafifest file and write to site.themes array
        if (Cache::contains($theme_cache_id)) {
            Registry::set('themes.'.Registry::get('system.theme'), Cache::fetch($theme_cache_id));
        } else {
            if (Filesystem::fileExists($theme_settings = PATH['themes'] . '/' . $theme . '/' . 'settings.yaml') and
                Filesystem::fileExists($theme_config = PATH['themes'] . '/' . $theme . '/' . $theme . '.yaml')) {
                $theme_settings = Yaml::parseFile($theme_settings);
                $theme_config = Yaml::parseFile($theme_config);
                $_theme = array_merge($theme_settings, $theme_config);
                Registry::set('themes.'.Registry::get('system.theme'), $_theme);
                Cache::save($theme_cache_id, $_theme);
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
        if (Filesystem::fileExists(PATH['themes'] . '/' . Registry::get('system.theme') . '/views/' . $template . View::$view_ext)) {
            $template = PATH['themes'] . '/' . Registry::get('system.theme') . '/views/' . $template;
        } else {
            $template = PATH['plugins'] . '/' . $template;
        }

        // Return template
        return new View($template, $variables);
    }

    /**
     * Get templates for current theme
     *
     * @access public
     * @return array
     */
    public static function getTemplates() : array
    {
        $templates = [];

        // Get templates files
        $_templates = Filesystem::getFilesList(PATH['themes'] . '/' . Registry::get('system.theme') . '/views/templates/', 'php');

        // If there is any template file then go...
        if (count($_templates) > 0) {
            foreach ($_templates as $template) {
                if (!is_bool(Themes::_strrevpos($template, '/templates/'))) {
                    $template_name = str_replace('.php', '', substr($template, Themes::_strrevpos($template, '/templates/')+strlen('/templates/')));
                    $templates[$template_name] = $template_name;
                }
            }
        }

        // return templates
        return $templates;
    }

    /**
     * Get templates blueprints for current theme
     *
     * @access public
     * @return array
     */
    public static function getTemplatesBlueprints() : array
    {
        $blueprints = [];

        // Get blueprints files
        $_blueprints = Filesystem::getFilesList(PATH['themes'] . '/' . Registry::get('system.theme') . '/blueprints/', 'yaml');

        // If there is any template file then go...
        if (count($_blueprints) > 0) {
            foreach ($_blueprints as $blueprint) {
                if (!is_bool(Themes::_strrevpos($blueprint, '/blueprints/'))) {
                    $blueprint_name = str_replace('.php', '', substr($blueprint, Themes::_strrevpos($blueprint, '/blueprints/')+strlen('/blueprints/')));
                    $blueprints[$blueprint_name] = $blueprint_name;
                }
            }
        }

        // return blueprints
        return $blueprints;
    }

    /**
     * _strrevpos
     */
    private static function _strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
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
