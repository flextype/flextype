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

use Symfony\Component\Yaml\Yaml;

class Plugins
{
    /**
     * An instance of the Cache class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        static::init();
    }

    /**
     * Init Plugins
     *
     * @access protected
     * @return void
     */
    protected static function init() : void
    {
        // Plugin manifest
        $plugin_manifest = [];

        // Plugin cache id
        $plugins_cache_id = '';

        // Get Plugins List
        $plugins_list = Config::get('site.plugins');

        // If Plugins List isnt empty then create plugin cache ID
        if (is_array($plugins_list) && count($plugins_list) > 0) {

            // Go through...
            foreach ($plugins_list as $plugin) {
                if (Flextype::filesystem()->exists($_plugin = PLUGINS_PATH . '/' . $plugin . '/' . $plugin . '.yml')) {
                    $plugins_cache_id .= filemtime($_plugin);
                }
            }

            // Create Unique Cache ID for Plugins
            $plugins_cache_id = md5('plugins' . PLUGINS_PATH . $plugins_cache_id);
        }

        // Get plugins list from cache or scan plugins folder and create new plugins cache item
        if (Cache::driver()->contains($plugins_cache_id)) {
            Config::set('plugins', Cache::driver()->fetch($plugins_cache_id));
        } else {

            // If Plugins List isnt empty
            if (is_array($plugins_list) && count($plugins_list) > 0) {

                // Go through...
                foreach ($plugins_list as $plugin) {

                    if (Flextype::filesystem()->exists($_plugin_manifest = PLUGINS_PATH . '/' . $plugin . '/' . $plugin . '.yml')) {
                        $plugin_manifest = Yaml::parseFile($_plugin_manifest);
                    }

                    $_plugins_config[basename($_plugin_manifest, '.yml')] = $plugin_manifest;
                }

                Config::set('plugins', $_plugins_config);
                Cache::driver()->save($plugins_cache_id, $_plugins_config);
            }
        }

        // Include enabled plugins
        if (is_array(Config::get('plugins')) && count(Config::get('plugins')) > 0) {
            foreach (Config::get('plugins') as $plugin_name => $plugin) {
                if (Config::get('plugins.'.$plugin_name.'.enabled')) {
                    include_once PLUGINS_PATH .'/'. $plugin_name .'/'. $plugin_name . '.php';
                }
            }
        }

        Events::dispatch('onPluginsInitialized');
    }

    /**
     * Return the Plugins instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Plugins();
    }
}
