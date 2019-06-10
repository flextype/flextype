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
use Flextype\Component\I18n\I18n;

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
        $this->locales = JsonParser::decode(Filesystem::read(ROOT_DIR . '/flextype/config/locales.json'));
    }

    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Init Plugins
     *
     * @access private
     * @return void
     */
    public function init($flextype, $app) : void
    {
        // Set empty plugins item
        $this->flextype['registry']->set('plugins', []);

        // Get Plugins List
        $_plugins_list = Filesystem::listContents(PATH['plugins']);
        $plugins_list = [];

        foreach($_plugins_list as $plugin) {
            if ($plugin['type'] == 'dir') {
                $plugins_list[] = $plugin;
            }
        }

        // Get plugins cache ID
        $plugins_cache_id = $this->getPluginsCacheID($plugins_list);

        // If Plugins List isnt empty then create plugin cache ID
        if (is_array($plugins_list) && count($plugins_list) > 0) {

            // Get plugins list from cache or scan plugins folder and create new plugins cache item
            if ($this->flextype['cache']->contains($plugins_cache_id)) {
                $this->flextype['registry']->set('plugins', $this->flextype['cache']->fetch($plugins_cache_id));
            } else {

                // If Plugins List isnt empty
                if (is_array($plugins_list) && count($plugins_list) > 0) {

                    // Init plugin configs
                    $_plugins_config = [];
                    $plugin_settings = [];
                    $plugin_config = [];

                    // Go through...
                    foreach ($plugins_list as $plugin) {
                        if (Filesystem::has($_plugin_settings = PATH['plugins'] . '/' . $plugin['dirname'] . '/settings.json')) {
                            if (($content = Filesystem::read($_plugin_settings)) === false) {
                                throw new \RuntimeException('Load file: ' . $_plugin_settings . ' - failed!');
                            } else {
                                $plugin_settings = JsonParser::decode($content);
                            }
                        }

                        if (Filesystem::has($_plugin_config = PATH['plugins'] . '/' . $plugin['dirname'] . '/plugin.json')) {
                            if (($content = Filesystem::read($_plugin_config)) === false) {
                                throw new \RuntimeException('Load file: ' . $_plugin_config . ' - failed!');
                            } else {
                                $plugin_config = JsonParser::decode($content);
                            }
                        }

                        $_plugins_config[$plugin['dirname']] = array_merge($plugin_settings, $plugin_config);
                    }

                    $this->flextype['registry']->set('plugins', $_plugins_config);
                    $this->flextype['cache']->save($plugins_cache_id, $_plugins_config);
                }
            }

            $this->createPluginsDictionary($plugins_list);

            $this->includeEnabledPlugins($flextype, $app);

            $this->flextype['emitter']->emit('onPluginsInitialized');
        }
    }

    /**
     * Create plugins dictionary
     *
     * @param  array $plugins_list Plugins list
     * @access protected
     * @return void
     */
    private function createPluginsDictionary(array $plugins_list) : void
    {
        if (is_array($plugins_list) && count($plugins_list) > 0) {
            foreach ($this->locales as $locale => $locale_title) {
                foreach ($plugins_list as $plugin) {
                    $language_file = PATH['plugins'] . '/' . $plugin['dirname'] . '/lang/' . $locale . '.json';
                    if (Filesystem::has($language_file)) {
                        if (($content = Filesystem::read($language_file)) === false) {
                            throw new \RuntimeException('Load file: ' . $language_file . ' - failed!');
                        } else {
                            I18n::add(JsonParser::decode($content), $locale);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get plugins cache ID
     *
     * @param  array $plugins_list Plugins list
     * @access protected
     * @return string
     */
    private function getPluginsCacheID(array $plugins_list) : string
    {
        // Plugin cache id
        $_plugins_cache_id = '';

        // Go through...
        if (is_array($plugins_list) && count($plugins_list) > 0) {
            foreach ($plugins_list as $plugin) {
                if (Filesystem::has($_plugin_settings = PATH['plugins'] . '/' . $plugin['dirname'] . '/settings.json') and
                    Filesystem::has($_plugin_config = PATH['plugins'] . '/' . $plugin['dirname'] . '/plugin.json')) {
                    $_plugins_cache_id .= filemtime($_plugin_settings) . filemtime($_plugin_config);
                }
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
     * @return void
     */
    private function includeEnabledPlugins($flextype, $app) : void
    {
        if (is_array($this->flextype['registry']->get('plugins')) && count($this->flextype['registry']->get('plugins')) > 0) {
            foreach ($this->flextype['registry']->get('plugins') as $plugin_name => $plugin) {
                if ($this->flextype['registry']->get('plugins.' . $plugin_name . '.enabled')) {
                    include_once PATH['plugins'] . '/' . $plugin_name . '/bootstrap.php';
                }
            }
        }
    }
}
