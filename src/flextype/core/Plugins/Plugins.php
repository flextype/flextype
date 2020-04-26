<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\I18n\I18n;
use Composer\Semver\Comparator;
use RuntimeException;
use function array_replace_recursive;
use function count;
use function filemtime;
use function is_array;
use function md5;
use function trim;

class Plugins
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Locales array
     *
     * @var array
     */
    private $locales = [];

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype, $app)
    {
        $this->flextype = $flextype;
        $this->locales  = $this->flextype['serializer']->decode(Filesystem::read(ROOT_DIR . '/src/flextype/config/locales.yaml'), 'yaml');
    }

    /**
     * Get locales
     *
     * @return array
     *
     * @access public
     */
    public function getLocales() : array
    {
        return $this->locales;
    }

    /**
     * Init Plugins
     *
     * @access private
     */
    public function init($flextype, $app) : void
    {
        // Set empty plugins item
        $this->flextype['registry']->set('plugins', []);

        // Set locale
        $locale = $this->flextype['registry']->get('flextype.settings.locale');

        // Get plugins list
        $plugins_list = $this->getPluginsList();

        // Get plugins Cache ID
        $plugins_cache_id = $this->getPluginsCacheID($plugins_list);

        // If Plugins List isnt empty then continue
        if (! is_array($plugins_list) || count($plugins_list) <= 0) {
            return;
        }

        // Get plugins from cache or scan plugins folder and create new plugins cache item
        if ($this->flextype['cache']->contains($plugins_cache_id)) {
            $this->flextype['registry']->set('plugins', $this->flextype['cache']->fetch($plugins_cache_id));

            if ($this->flextype['cache']->contains($locale)) {
                I18n::add($this->flextype['cache']->fetch($locale), $locale);
            } else {
                // Save plugins dictionary
                $dictionary = $this->getPluginsDictionary($plugins_list, $locale);
                $this->flextype['cache']->save($locale, $dictionary[$locale]);
            }

        } else {
            // Init plugin configs
            $plugins                 = [];
            $plugin_settings         = [];
            $plugin_manifest         = [];
            $default_plugin_settings = [];
            $project_plugin_settings    = [];
            $default_plugin_manifest = [];

            // Go through...
            foreach ($plugins_list as $plugin) {

                // Set plugin settings directory
                $project_plugin_settings_dir = PATH['project'] . '/config/plugins/' . $plugin['dirname'];

                // Set default plugin settings and manifest files
                $default_plugin_settings_file = PATH['project'] . '/plugins/' . $plugin['dirname'] . '/settings.yaml';
                $default_plugin_manifest_file = PATH['project'] . '/plugins/' . $plugin['dirname'] . '/plugin.yaml';

                // Set project plugin settings file
                $project_plugin_settings_file = PATH['project'] . '/config/plugins/' . $plugin['dirname'] . '/settings.yaml';

                // Create project plugin settings directory
                ! Filesystem::has($project_plugin_settings_dir) and Filesystem::createDir($project_plugin_settings_dir);

                // Check if default plugin settings file exists
                if (! Filesystem::has($default_plugin_settings_file)) {
                    throw new RuntimeException('Load ' . $plugin['dirname'] . ' plugin settings - failed!');
                }

                // Get default plugin settings content
                $default_plugin_settings_file_content = Filesystem::read($default_plugin_settings_file);
                $default_plugin_settings              = $this->flextype['serializer']->decode($default_plugin_settings_file_content, 'yaml');

                // Create project plugin settings file
                ! Filesystem::has($project_plugin_settings_file) and Filesystem::write($project_plugin_settings_file, $default_plugin_settings_file_content);

                // Get project plugin settings content
                $project_plugin_settings_file_content = Filesystem::read($project_plugin_settings_file);

                if (trim($project_plugin_settings_file_content) === '') {
                    $project_plugin_settings = [];
                } else {
                    $project_plugin_settings = $this->flextype['serializer']->decode($project_plugin_settings_file_content, 'yaml');
                }

                // Check if default plugin manifest file exists
                if (! Filesystem::has($default_plugin_manifest_file)) {
                    RuntimeException('Load ' . $plugin['dirname'] . ' plugin manifest - failed!');
                }

                // Get default plugin manifest content
                $default_plugin_manifest_file_content = Filesystem::read($default_plugin_manifest_file);
                $default_plugin_manifest              = $this->flextype['serializer']->decode($default_plugin_manifest_file_content, 'yaml');

                // Merge plugin settings and manifest data
                $plugins[$plugin['dirname']]['manifest'] = $default_plugin_manifest;
                $plugins[$plugin['dirname']]['settings'] = array_replace_recursive($default_plugin_settings, $project_plugin_settings);

                // Check if is not set plugin priority
                if (! isset($plugins[$plugin['dirname']]['settings']['priority'])) {

                    // Set default plugin priority = 1
                    $plugins[$plugin['dirname']]['settings']['priority'] = 100;
                }

                // Set tmp _priority field for sorting
                $plugins[$plugin['dirname']]['_priority'] = $plugins[$plugin['dirname']]['settings']['priority'];
            }

            // Sort plugins list by priority.
            $plugins = Arr::sort($plugins, '_priority', 'DESC');

            // ... and delete tmp _priority field for sorting
            foreach ($plugins as $plugin_name => $plugin_data) {
                Arr::delete($plugins, $plugin_name . '._priority');
            }

            // Get Valid Plugins Dependencies
            $plugins = $this->getValidPluginsDependencies($plugins);

            // Save plugins list
            $this->flextype['registry']->set('plugins', $plugins);
            $this->flextype['cache']->save($plugins_cache_id, $plugins);

            // Save plugins dictionary
            $dictionary = $this->getPluginsDictionary($plugins_list, $locale);
            $this->flextype['cache']->save($locale, $dictionary[$locale]);

        }

        $this->includeEnabledPlugins($flextype, $app);

        $this->flextype['emitter']->emit('onPluginsInitialized');
    }

    /**
     * Get plugins dictionary
     *
     * @param  array $plugins_list Plugins list
     *
     * @access protected
     */
    private function getPluginsDictionary(array $plugins_list, string $locale) : array
    {
        foreach ($plugins_list as $plugin) {
            $language_file = PATH['project'] . '/plugins/' . $plugin['dirname'] . '/lang/' . $locale . '.yaml';

            if (! Filesystem::has($language_file)) {
                continue;
            }

            if (($content = Filesystem::read($language_file)) === false) {
                throw new RuntimeException('Load file: ' . $language_file . ' - failed!');
            }

            $translates = $this->flextype['serializer']->decode($content, 'yaml');

            I18n::add($translates, $locale);
        }

        return I18n::$dictionary;
    }

    /**
     * Get plugins Cache ID
     *
     * @param  array $plugins_list Plugins list
     *
     * @access protected
     */
    private function getPluginsCacheID(array $plugins_list) : string
    {
        // Plugin cache id
        $_plugins_cache_id = '';

        // Go through...
        if (is_array($plugins_list) && count($plugins_list) > 0) {
            foreach ($plugins_list as $plugin) {
                $default_plugin_settings_file = PATH['project'] . '/plugins/' . $plugin['dirname'] . '/settings.yaml';
                $default_plugin_manifest_file = PATH['project'] . '/plugins/' . $plugin['dirname'] . '/plugin.yaml';
                $project_plugin_settings_file    = PATH['config'] . '/project/plugins/' . $plugin['dirname'] . '/settings.yaml';

                $f1 = Filesystem::has($default_plugin_settings_file) ? filemtime($default_plugin_settings_file) : '';
                $f2 = Filesystem::has($default_plugin_manifest_file) ? filemtime($default_plugin_manifest_file) : '';
                $f3 = Filesystem::has($project_plugin_settings_file) ? filemtime($project_plugin_settings_file) : '';

                $_plugins_cache_id .= $f1 . $f2 . $f3;
            }
        }

        // Create Unique Cache ID for Plugins
        $plugins_cache_id = md5('plugins' . PATH['project'] . '/plugins/' . $_plugins_cache_id);

        // Return plugin cache id
        return $plugins_cache_id;
    }

    /**
     * Get valid plugins dependencies
     *
     * @param  array $plugins Plugins list
     *
     * @access protected
     */
    public function getValidPluginsDependencies($plugins) : array
    {
        foreach ($plugins as $plugin_name => $plugin_data) {
            if (isset($plugin_data['manifest']['dependencies']['flextype']) &&
                      Comparator::equalTo($plugin_data['manifest']['dependencies']['flextype'], $this->flextype['registry']->get('flextype.manifest.version'))) {
                $plugins[$plugin_name] = $plugin_data;
            } else {
                unset($plugins[$plugin_name]);
            }
        }

        foreach ($plugins as $plugin_name => $plugin_data) {

            $allowed_plugin = true;

            if (isset($plugin_data['manifest']['dependencies']['plugins'])) {

                foreach ($plugin_data['manifest']['dependencies']['plugins'] as $dependencies_plugin_name => $plugin_value) {

                     if (isset($plugins[$dependencies_plugin_name]) && $plugin_value == $plugins[$dependencies_plugin_name]['manifest']['version'] ) {

                     } else {
                         $allowed_plugin = false;
                         unset($plugins[$plugin_name]);
                     }
                }

                if ($allowed_plugin) {
                    $plugins[$plugin_name] = $plugin_data;
                }

            } else {
                $plugins[$plugin_name] = $plugin_data;
            }
        }

        return $plugins;
    }

    /**
     * Get plugins list
     *
     * @return array
     *
     * @access public
     */
    public function getPluginsList() : array
    {
        // Get Plugins List
        $plugins_list = [];

        foreach (Filesystem::listContents(PATH['project'] . '/plugins/') as $plugin) {
            if ($plugin['type'] !== 'dir') {
                continue;
            }

            $plugins_list[] = $plugin;
        }

        return $plugins_list;
    }

    /**
     * Include enabled plugins
     *
     * @access protected
     */
    private function includeEnabledPlugins($flextype, $app) : void
    {
        if (! is_array($this->flextype['registry']->get('plugins')) || count($this->flextype['registry']->get('plugins')) <= 0) {
            return;
        }

        foreach ($this->flextype['registry']->get('plugins') as $plugin_name => $plugin) {
            if (! $this->flextype['registry']->get('plugins.' . $plugin_name . '.settings.enabled')) {
                continue;
            }

            include_once PATH['project'] . '/plugins/' . $plugin_name . '/bootstrap.php';
        }
    }
}
