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
use RuntimeException;
use function array_merge;
use function count;
use function filemtime;
use function is_array;
use function md5;

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
        $this->locales  = Parser::decode(Filesystem::read(ROOT_DIR . '/flextype/config/locales.yaml'), 'yaml');
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

        // Get Plugins List
        $plugins_list = [];

        foreach (Filesystem::listContents(PATH['plugins']) as $plugin) {
            if ($plugin['type'] !== 'dir') {
                continue;
            }

            $plugins_list[] = $plugin;
        }

        // Get plugins cache ID
        $plugins_cache_id = $this->getPluginsCacheID($plugins_list);

        // If Plugins List isnt empty then create plugin cache ID
        if (! is_array($plugins_list) || count($plugins_list) <= 0) {
            return;
        }

        // Get plugins list from cache or scan plugins folder and create new plugins cache item
        if ($this->flextype['cache']->contains($plugins_cache_id)) {
            $this->flextype['registry']->set('plugins', $this->flextype['cache']->fetch($plugins_cache_id));
        } else {
            // If Plugins List isnt empty
            if (is_array($plugins_list) && count($plugins_list) > 0) {
                // Init plugin configs
                $plugins         = [];
                $plugin_settings = [];
                $plugin_manifest = [];

                // Go through...
                foreach ($plugins_list as $plugin) {
                    if (Filesystem::has($_plugin_settings = PATH['plugins'] . '/' . $plugin['dirname'] . '/settings.yaml')) {
                        if (($content = Filesystem::read($_plugin_settings)) === false) {
                            throw new RuntimeException('Load file: ' . $_plugin_settings . ' - failed!');
                        }

                        $plugin_settings = Parser::decode($content, 'yaml');
                    }

                    if (Filesystem::has($_plugin_manifest = PATH['plugins'] . '/' . $plugin['dirname'] . '/plugin.yaml')) {
                        if (($content = Filesystem::read($_plugin_manifest)) === false) {
                            throw new RuntimeException('Load file: ' . $_plugin_manifest . ' - failed!');
                        }

                        $plugin_manifest = Parser::decode($content, 'yaml');
                    }

                    $plugins[$plugin['dirname']] = array_merge($plugin_settings, $plugin_manifest);

                    // Set default plugin priority 0
                    if (isset($plugins[$plugin['dirname']]['priority'])) {
                        continue;
                    }

                    $plugins[$plugin['dirname']]['priority'] = 0;
                }

                // Sort plugins list by priority.
                $plugins = Arr::sort($plugins, 'priority', 'DESC');

                $this->flextype['registry']->set('plugins', $plugins);
                $this->flextype['cache']->save($plugins_cache_id, $plugins);
            }
        }

        $this->createPluginsDictionary($plugins_list);

        $this->includeEnabledPlugins($flextype, $app);

        $this->flextype['emitter']->emit('onPluginsInitialized');
    }

    /**
     * Create plugins dictionary
     *
     * @param  array $plugins_list Plugins list
     *
     * @access protected
     */
    private function createPluginsDictionary(array $plugins_list) : void
    {
        if (! is_array($plugins_list) || count($plugins_list) <= 0) {
            return;
        }

        foreach ($this->locales as $locale => $locale_title) {
            foreach ($plugins_list as $plugin) {
                $language_file = PATH['plugins'] . '/' . $plugin['dirname'] . '/lang/' . $locale . '.yaml';
                if (! Filesystem::has($language_file)) {
                    continue;
                }

                if (($content = Filesystem::read($language_file)) === false) {
                    throw new RuntimeException('Load file: ' . $language_file . ' - failed!');
                }

                I18n::add(Parser::decode($content, 'yaml'), $locale);
            }
        }
    }

    /**
     * Get plugins cache ID
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
                if (! Filesystem::has($_plugin_settings = PATH['plugins'] . '/' . $plugin['dirname'] . '/settings.yaml') or
                    ! Filesystem::has($_plugin_manifest = PATH['plugins'] . '/' . $plugin['dirname'] . '/plugin.yaml')) {
                    continue;
                }

                $_plugins_cache_id .= filemtime($_plugin_settings) . filemtime($_plugin_manifest);
            }
        }

        // Create Unique Cache ID for Plugins
        $plugins_cache_id = md5('plugins' . PATH['plugins'] . '/' . $_plugins_cache_id);

        // Return plugin cache id
        return $plugins_cache_id;
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
            if (! $this->flextype['registry']->get('plugins.' . $plugin_name . '.enabled')) {
                continue;
            }

            include_once PATH['plugins'] . '/' . $plugin_name . '/bootstrap.php';
        }
    }
}
