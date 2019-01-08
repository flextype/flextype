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
        $theme = Registry::get('settings.theme');

        // Set empty themes items
        Registry::set('themes', []);

        // Create Unique Cache ID for Theme
        $theme_cache_id = md5('theme' . filemtime(PATH['themes'] .'/'. $theme . '/' . 'settings.yaml') .
                                        filemtime(PATH['themes'] .'/'. $theme . '/' . $theme . '.yaml'));

        // Get Theme mafifest file and write to settings.themes array
        if (Cache::contains($theme_cache_id)) {
            Registry::set('themes.'.Registry::get('settings.theme'), Cache::fetch($theme_cache_id));
        } else {
            if (Filesystem::fileExists($theme_settings = PATH['themes'] . '/' . $theme . '/' . 'settings.yaml') and
                Filesystem::fileExists($theme_config = PATH['themes'] . '/' . $theme . '/' . $theme . '.yaml')) {
                $theme_settings = YamlParser::decode(Filesystem::getFileContent($theme_settings));
                $theme_config = YamlParser::decode(Filesystem::getFileContent($theme_config));
                $_theme = array_merge($theme_settings, $theme_config);
                Registry::set('themes.'.Registry::get('settings.theme'), $_theme);
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
        if (Filesystem::fileExists(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $template . View::$view_ext)) {
            $template = PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $template;
        } else {
            $template = PATH['plugins'] . '/' . $template;
        }

        // Return template
        return new View($template, $variables);
    }

    /**
     * Get partials for current theme
     *
     * @access public
     * @return array
     */
    public static function getPartials() : array
    {
        $partials = [];

        // Get templates files
        $_partials = Filesystem::getFilesList(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/partials/', 'php');

        // If there is any template file then go...
        if (count($_partials) > 0) {
            foreach ($_partials as $partial) {
                if (!is_bool(Themes::_strrevpos($partial, '/partials/'))) {
                    $partial_name = str_replace('.php', '', substr($partial, Themes::_strrevpos($partial, '/partials/')+strlen('/partials/')));
                    $partials[$partial_name] = $partial_name;
                }
            }
        }

        // return partials
        return $partials;
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
        $_templates = Filesystem::getFilesList(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/', 'php');

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
     * Get Fieldsets for current theme
     *
     * @access public
     * @return array
     */
    public static function getFieldsets() : array
    {
        $fieldsets = [];

        // Get fieldsets files
        $_fieldsets = Filesystem::getFilesList(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/', 'yaml');

        // If there is any template file then go...
        if (count($_fieldsets) > 0) {
            foreach ($_fieldsets as $fieldset) {
                if (!is_bool(Themes::_strrevpos($fieldset, '/fieldsets/'))) {
                    $fieldset_name = str_replace('.yaml', '', substr($fieldset, Themes::_strrevpos($fieldset, '/fieldsets/')+strlen('/fieldsets/')));
                    $fieldset = YamlParser::decode(Filesystem::getFileContent($fieldset));
                    $fieldsets[$fieldset_name] = $fieldset['title'];
                }
            }
        }

        // return fieldsets
        return $fieldsets;
    }

    /**
     * _strrevpos
     *
     * @param string $instr instr
     * @param string $needle needle
     *
     * @return bool
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
