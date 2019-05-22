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

    public function init($flextype, $app)
    {

        // Get current theme
        $theme = $this->flextype['registry']->get('settings.theme');

        // Set empty themes items
        $this->flextype['registry']->set('themes', []);

        // Create Unique Cache ID for Theme
        $theme_cache_id = md5('theme' . filemtime(PATH['themes'] . '/' . $theme . '/' . 'settings.json') .
                                        filemtime(PATH['themes'] . '/' . $theme . '/' . $theme . '.json'));

        // Get Theme mafifest file and write to settings.themes array
        if ($this->flextype['cache']->contains($theme_cache_id)) {
            $this->flextype['registry']->set('themes.' . $this->flextype['registry']->get('settings.theme'), $this->flextype['cache']->fetch($theme_cache_id));
        } else {
            if (Filesystem::has($theme_settings = PATH['themes'] . '/' . $theme . '/' . 'settings.json') and
                Filesystem::has($theme_config = PATH['themes'] . '/' . $theme . '/' . $theme . '.json')) {
                $theme_settings = JsonParser::decode(Filesystem::read($theme_settings));
                $theme_config = JsonParser::decode(Filesystem::read($theme_config));
                $_theme = array_merge($theme_settings, $theme_config);
                $this->flextype['registry']->set('themes.' . $this->flextype['registry']->get('settings.theme'), $_theme);
                $this->flextype['cache']->save($theme_cache_id, $_theme);
            }
        }
    }


    /**
     * Get partials for current theme
     *
     * @access public
     * @return array
     */
    public function getPartials() : array
    {
        $partials = [];

        // Get partials files
        $_partials = Filesystem::listContents(PATH['themes'] . '/' . $this->flextype['registry']->get('settings.theme') . '/views/partials/');

        // If there is any partials file then go...
        if (count($_partials) > 0) {
            foreach ($_partials as $partial) {
                if ($partial['type'] == 'file' && $partial['extension'] == 'html') {
                    $partials[$partial['basename']] = $partial['basename'];
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
    public function getTemplates() : array
    {
        $templates = [];

        // Get templates files
        $_templates = Filesystem::listContents(PATH['themes'] . '/' . $this->flextype['registry']->get('settings.theme') . '/templates/');

        // If there is any template file then go...
        if (count($_templates) > 0) {
            foreach ($_templates as $template) {
                if ($template['type'] == 'file' && $template['extension'] == 'html') {
                    $templates[$template['basename']] = $template['basename'];
                }
            }
        }

        // return templates
        return $templates;
    }
}
