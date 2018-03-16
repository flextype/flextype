<?php namespace Rawilum;

use Symfony\Component\Yaml\Yaml;

/**
 * @package Rawilum
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://rawilum.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugins
{
    /**
     * An instance of the Cache class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Init Plugins
     *
     * @access public
     * @return mixed
     */
    protected function __construct()
    {
        // Plugin manifest
        $plugin_manifest = [];

        // Get Plugins List
        $plugins_list = Config::get('site.plugins');

        // If Plugins List isnt empty
        if (is_array($plugins_list) && count($plugins_list) > 0) {

            // Go through...
            foreach ($plugins_list as $plugin) {
                if (file_exists($_plugin_manifest = PLUGINS_PATH . '/' . $plugin . '/' . $plugin . '.yml')) {
                    $plugin_manifest = Yaml::parse(file_get_contents($_plugin_manifest));
                }

                $_plugins_config[basename($_plugin_manifest)] = $plugin_manifest;
            }
        }

        if (is_array(Config::get('site.plugins')) && count(Config::get('site.plugins')) > 0) {
            foreach (Config::get('site.plugins') as $plugin_id => $plugin_name) {
                include_once PLUGINS_PATH .'/'. $plugin_name .'/'. $plugin_name . '.php';
            }
        }
    }

    /**
     * Initialize Rawilum Plugins
     *
     *  <code>
     *      Plugins::init();
     *  </code>
     *
     * @access public
     * @return object
     */
    public static function init()
    {
        return !isset(self::$instance) and self::$instance = new Plugins();
    }
}
