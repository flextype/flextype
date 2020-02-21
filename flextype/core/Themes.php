<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use RuntimeException;
use function array_merge;
use function array_replace_recursive;
use function count;
use function filemtime;
use function is_array;
use function md5;

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
    public function init($flextype, $app) : void
    {
        // Set empty themes list item
        $this->flextype['registry']->set('themes', []);

        // Get themes list
        $themes_list = $this->getThemes();

        // If Themes List isnt empty then continue
        if (! is_array($themes_list) || count($themes_list) <= 0) {
            return;
        }

        // Get themes cache ID
        $themes_cache_id = $this->getThemesCacheID($themes_list);

        // Get themes list from cache or scan themes folder and create new themes cache item in the registry
        if ($this->flextype['cache']->contains($themes_cache_id)) {
            $this->flextype['registry']->set('themes', $this->flextype['cache']->fetch($themes_cache_id));
        } else {
            $themes                 = [];
            $themes_settings        = [];
            $themes_manifest        = [];
            $default_theme_settings = [];
            $site_theme_settings    = [];
            $default_theme_manifest = [];
            $site_theme_manifest    = [];

            // Go through the themes list...
            foreach ($themes_list as $theme) {

                // Set custom theme directory
                $custom_theme_settings_dir = PATH['config']['site'] . '/themes/' . $theme['dirname'];

                // Set default theme settings and manifest files
                $default_theme_settings_file = PATH['themes'] . '/' . $theme['dirname'] . '/settings.yaml';
                $default_theme_manifest_file = PATH['themes'] . '/' . $theme['dirname'] . '/theme.yaml';

                // Set custom theme settings and manifest files
                $custom_theme_settings_file = PATH['config']['site'] . '/themes/' . $theme['dirname'] . '/settings.yaml';
                $custom_theme_manifest_file = PATH['config']['site'] . '/themes/' . $theme['dirname'] . '/theme.yaml';

                // Create custom theme settings directory
                ! Filesystem::has($custom_theme_settings_dir)  and Filesystem::createDir($custom_theme_settings_dir);

                // Check if default theme settings file exists
                if (! Filesystem::has($default_theme_settings_file)) {
                    throw new RuntimeException('Load ' . $theme['dirname'] . ' theme settings - failed!');
                }

                // Get default theme manifest content
                $default_theme_settings_file_content = Filesystem::read($default_theme_settings_file);
                if (trim($default_theme_settings_file_content) === '') {
                    $default_theme_settings = [];
                } else {
                    $default_theme_settings = $this->flextype['parser']->decode($default_theme_settings_file_content, 'yaml');
                }

                // Create custom theme settings file
                ! Filesystem::has($custom_theme_settings_file) and Filesystem::write($custom_theme_settings_file, $default_theme_settings_file_content);

                // Get custom theme settings content
                $custom_theme_settings_file_content = Filesystem::read($custom_theme_settings_file);
                if (trim($custom_theme_settings_file_content) === '') {
                    $custom_theme_settings = [];
                } else {
                    $custom_theme_settings = $this->flextype['parser']->decode($custom_theme_settings_file_content, 'yaml');
                }

                // Check if default theme manifest file exists
                if (! Filesystem::has($default_theme_manifest_file)) {
                    RuntimeException('Load ' . $theme['dirname'] . ' theme manifest - failed!');
                }

                // Get default theme manifest content
                $default_theme_manifest_file_content = Filesystem::read($default_theme_manifest_file);
                $default_theme_manifest              = $this->flextype['parser']->decode($default_theme_manifest_file_content, 'yaml');

                // Create custom theme file
                ! Filesystem::has($custom_theme_manifest_file) and Filesystem::write($custom_theme_manifest_file, $default_theme_manifest_file_content);

                // Get custom theme manifest content
                $custom_theme_manifest_file_content = Filesystem::read($custom_theme_manifest_file);
                if (trim($custom_theme_manifest_file_content) === '') {
                    $custom_theme_manifest = [];
                } else {
                   $custom_theme_manifest = $this->flextype['parser']->decode($custom_theme_manifest_file_content, 'yaml');
                }

                // Merge theme settings and manifest data
                $themes[$theme['dirname']] = array_merge(
                    array_replace_recursive($default_theme_settings, $custom_theme_settings),
                    array_replace_recursive($default_theme_manifest, $custom_theme_manifest)
                );
            }

            // Save parsed themes list in the registry themes
            $this->flextype['registry']->set('themes', $themes);

            // Save parsed themes list in the cache
            $this->flextype['cache']->save($themes_cache_id, $themes);
        }

        // Emit onThemesInitialized
        $this->flextype['emitter']->emit('onThemesInitialized');
    }

    /**
     * Get Themes Cache ID
     *
     * @param  array $themes_list Themes list
     *
     * @access protected
     */
    private function getThemesCacheID(array $themes_list) : string
    {
        // Themes Cache ID
        $_themes_cache_id = '';

        // Go through...
        if (is_array($themes_list) && count($themes_list) > 0) {
            foreach ($themes_list as $theme) {
                $default_theme_settings_file = PATH['themes'] . '/' . $theme['dirname'] . '/settings.yaml';
                $default_theme_manifest_file = PATH['themes'] . '/' . $theme['dirname'] . '/theme.yaml';
                $site_theme_settings_file    = PATH['config']['site'] . '/themes/' . $theme['dirname'] . '/settings.yaml';
                $site_theme_manifest_file    = PATH['config']['site'] . '/themes/' . $theme['dirname'] . '/theme.yaml';

                $f1 = Filesystem::has($default_theme_settings_file) ? filemtime($default_theme_settings_file) : '';
                $f2 = Filesystem::has($default_theme_manifest_file) ? filemtime($default_theme_manifest_file) : '';
                $f3 = Filesystem::has($site_theme_settings_file) ? filemtime($site_theme_settings_file) : '';
                $f4 = Filesystem::has($site_theme_manifest_file) ? filemtime($site_theme_manifest_file) : '';

                $_themes_cache_id .= $f1 . $f2 . $f3 . $f4;
            }
        }

        // Create Unique Cache ID for Themes
        $themes_cache_id = md5('themes' . PATH['themes'] . '/' . $_themes_cache_id);

        // Return themes cache id
        return $themes_cache_id;
    }

    /**
     * Get list of themes
     *
     * @return array
     *
     * @access public
     */
    public function getThemes() : array
    {
        // Init themes list
        $themes_list = [];

        // Get themes list
        $_themes_list = Filesystem::listContents(PATH['themes']);

        // Go through founded themes
        foreach ($_themes_list as $theme) {
            if ($theme['type'] !== 'dir' || ! Filesystem::has($theme['path'] . '/' . 'theme.yaml')) {
                continue;
            }

            $themes_list[] = $theme;
        }

        return $themes_list;
    }

    /**
     * Get partials for theme
     *
     * @param string $theme Theme id
     *
     * @return array
     *
     * @access public
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
                if ($partial['type'] !== 'file' || $partial['extension'] !== 'html') {
                    continue;
                }

                $partials_list[] = $partial;
            }
        }

        // return partials
        return $partials_list;
    }

    /**
     * Get templates for theme
     *
     * @param string $theme Theme id
     *
     * @return array
     *
     * @access public
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
                if ($template['type'] !== 'file' || $template['extension'] !== 'html') {
                    continue;
                }

                $templates_list[] = $template;
            }
        }

        // return templates
        return $templates_list;
    }
}
