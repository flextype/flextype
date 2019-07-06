<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;

class Themes
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Init themes
     */
    public function init($flextype, $app)
    {
        // Set empty themes list item
        $this->flextype['registry']->set('themes', []);

        // Get themes list
        $themes_list = $this->getThemes();

        // If themes list isnt empty then create themes cache ID and go through the themes list...
        if (is_array($themes_list) && count($themes_list) > 0) {

            // Get themes cache ID
            $themes_cache_id = $this->getThemesCacheID($themes_list);

            // Get themes list from cache or scan themes folder and create new themes cache item in the registry
            if ($this->flextype['cache']->contains($themes_cache_id)) {
                $this->flextype['registry']->set('themes', $this->flextype['cache']->fetch($themes_cache_id));
            } else {

                    // Go through the themes list...
                    foreach ($themes_list as $theme) {

                        // Get theme settings
                        if (Filesystem::has($theme_settings_file = PATH['themes'] . '/' . $theme['dirname'] . '/settings.json')) {
                            if (($content = Filesystem::read($theme_settings_file)) === false) {
                                throw new \RuntimeException('Load file: ' . $theme_settings_file . ' - failed!');
                            } else {
                                $theme_settings = JsonParser::decode($content);
                            }
                        }

                        // Get theme manifest
                        if (Filesystem::has($theme_manifest_file = PATH['themes'] . '/' . $theme['dirname'] . '/theme.json')) {
                            if (($content = Filesystem::read($theme_manifest_file)) === false) {
                                throw new \RuntimeException('Load file: ' . $theme_manifest_file . ' - failed!');
                            } else {
                                $theme_manifest = JsonParser::decode($content);
                            }
                        }

                        $themes[$theme['dirname']] = array_merge($theme_settings, $theme_manifest);
                    }

                    // Save parsed themes list in the registry themes
                    $this->flextype['registry']->set('themes', $themes);

                    // Save parsed themes list in the cache
                    $this->flextype['cache']->save($themes_cache_id, $themes);
            }
        }

        // Emit onThemesInitialized
        $this->flextype['emitter']->emit('onThemesInitialized');
    }


    /**
     * Get themes cache ID
     *
     * @param  array $themes_list Themes list
     * @access protected
     * @return string
     */
    private function getThemesCacheID(array $themes_list) : string
    {
        // Themes cache id
        $_themes_cache_id = '';

        // Go through themes list...
        if (is_array($themes_list) && count($themes_list) > 0) {
            foreach ($themes_list as $theme) {
                if (Filesystem::has($_themes_settings = PATH['themes'] . '/' . $theme['dirname'] . '/settings.json') and
                    Filesystem::has($_themes_manifest = PATH['themes'] . '/' . $theme['dirname'] . '/plugin.json')) {
                    $_themes_cache_id .= filemtime($_themes_settings) . filemtime($_themes_manifest);
                }
            }
        }

        // Create Unique Cache ID for Themes
        $themes_cache_id = md5('themes' . PATH['themes'] . $_themes_cache_id);

        // Return themes cache id
        return $themes_cache_id;
    }


    /**
     * Get list of themes
     *
     * @access public
     * @return array
     */
    public function getThemes() : array
    {
        // Init themes list
        $themes_list = [];

        // Get themes list
        $_themes_list = Filesystem::listContents(PATH['themes']);

        // Go through founded themes
        foreach($_themes_list as $theme) {
            if ($theme['type'] == 'dir' && Filesystem::has($theme['path'] . '/' . 'theme.json')) {
                $themes_list[] = $theme;
            }
        }

        return $themes_list;
    }

    /**
     * Get partials for theme
     *
     * @access public
     * @param string $theme Theme id
     * @return array
     */
    public function getPartials(string $theme) : array
    {
        // Init partials list
        $partials_list = [];

        // Get partials files
        $_partials_list = Filesystem::listContents(PATH['themes'] . '/' . $theme . '/templates/partials/');

        // If there is any partials file then go...
        if (count($_partials_list) > 0) {
            foreach ($_partials_list as $partial) {
                if ($partial['type'] == 'file' && $partial['extension'] == 'html') {
                    $partials_list[] = $partial;
                }
            }
        }

        // return partials
        return $partials_list;
    }

    /**
     * Get templates for theme
     *
     * @access public
     * @param string $theme Theme id
     * @return array
     */
    public function getTemplates(string $theme) : array
    {
        // Init templates list
        $templates_list = [];

        // Get templates files
        $_templates_list = Filesystem::listContents(PATH['themes'] . '/' . $theme . '/templates/');

        // If there is any template file then go...
        if (count($_templates_list) > 0) {
            foreach ($_templates_list as $template) {
                if ($template['type'] == 'file' && $template['extension'] == 'html') {
                    $templates_list[] = $template;
                }
            }
        }

        // return templates
        return $templates_list;
    }
}
