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

    public function getLocales()
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
        $_plugins_list = Filesystem::listContents(PATH['plugins']);
        $plugins_list  = [];

        foreach ($_plugins_list as $plugin) {
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
                $_plugins_config = [];
                $plugin_settings = [];
                $plugin_config   = [];

                // Go through...
                foreach ($plugins_list as $plugin) {
                    if (Filesystem::has($_plugin_settings = PATH['plugins'] . '/' . $plugin['dirname'] . '/settings.json')) {
                        if (($content = Filesystem::read($_plugin_settings)) === false) {
                            throw new RuntimeException('Load file: ' . $_plugin_settings . ' - failed!');
                        }

                        $plugin_settings = JsonParser::decode($content);
                    }

                    if (Filesystem::has($_plugin_config = PATH['plugins'] . '/' . $plugin['dirname'] . '/plugin.json')) {
                        if (($content = Filesystem::read($_plugin_config)) === false) {
                            throw new RuntimeException('Load file: ' . $_plugin_config . ' - failed!');
                        }

                        $plugin_config = JsonParser::decode($content);
                    }

                    $_plugins_config[$plugin['dirname']] = array_merge($plugin_settings, $plugin_config);

                    // Set default plugin priority 0
                    if (isset($_plugins_config[$plugin['dirname']]['priority'])) {
                        continue;
                    }

                    $_plugins_config[$plugin['dirname']]['priority'] = 0;
                }

                // Sort plugins list by priority.
                $_plugins_config = Arr::sort($_plugins_config, 'priority', 'DESC');

                $this->flextype['registry']->set('plugins', $_plugins_config);
                $this->flextype['cache']->save($plugins_cache_id, $_plugins_config);
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
                $language_file = PATH['plugins'] . '/' . $plugin['dirname'] . '/lang/' . $locale . '.json';
                if (! Filesystem::has($language_file)) {
                    continue;
                }

                if (($content = Filesystem::read($language_file)) === false) {
                    throw new RuntimeException('Load file: ' . $language_file . ' - failed!');
                }

                I18n::add(JsonParser::decode($content), $locale);
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
                if (! Filesystem::has($_plugin_settings = PATH['plugins'] . '/' . $plugin['dirname'] . '/settings.json') or
                    ! Filesystem::has($_plugin_config = PATH['plugins'] . '/' . $plugin['dirname'] . '/plugin.json')) {
                    continue;
                }

                $_plugins_cache_id .= filemtime($_plugin_settings) . filemtime($_plugin_config);
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
