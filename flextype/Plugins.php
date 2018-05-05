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

use Flextype\Component\{Filesystem\Filesystem, Event\Event, I18n\I18n, Registry\Registry};
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
     * Locales array
     *
     * @var array
     */
    public static $locales = [
        'ar' => 'العربية',
        'bg' => 'Български',
        'ca' => 'Català',
        'cs' => 'Česky',
        'da' => 'Dansk',
        'de' => 'Deutsch',
        'el' => 'Ελληνικά',
        'en' => 'English',
        'es' => 'Español',
        'fa' => 'Farsi',
        'fi' => 'Suomi',
        'fr' => 'Français',
        'gl' => 'Galego',
        'ka-ge' => 'Georgian',
        'hu' => 'Magyar',
        'it' => 'Italiano',
        'id' => 'Bahasa Indonesia',
        'ja' => '日本語',
        'lt' => 'Lietuvių',
        'nl' => 'Nederlands',
        'no' => 'Norsk',
        'pl' => 'Polski',
        'pt' => 'Português',
        'pt-br' => 'Português do Brasil',
        'ru' => 'Русский',
        'sk' => 'Slovenčina',
        'sl' => 'Slovenščina',
        'sv' => 'Svenska',
        'sr' => 'Srpski',
        'tr' => 'Türkçe',
        'uk' => 'Українська',
        'zh-cn' => '简体中文',
    ];

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
        $plugins_list = Registry::get('site.plugins');

        // Set empty plugins item
        Registry::set('plugins', []);

        // If Plugins List isnt empty then create plugin cache ID
        if (is_array($plugins_list) && count($plugins_list) > 0) {

            // Go through...
            foreach ($plugins_list as $plugin) {
                if (Filesystem::fileExists($_plugin = PLUGINS_PATH . '/' . $plugin . '/' . $plugin . '.yaml')) {
                    $plugins_cache_id .= filemtime($_plugin);
                }
            }

            // Create Unique Cache ID for Plugins
            $plugins_cache_id = md5('plugins' . PLUGINS_PATH . $plugins_cache_id);
        }

        // Get plugins list from cache or scan plugins folder and create new plugins cache item
        if (Cache::driver()->contains($plugins_cache_id)) {
            Registry::set('plugins', Cache::driver()->fetch($plugins_cache_id));
        } else {

            // If Plugins List isnt empty
            if (is_array($plugins_list) && count($plugins_list) > 0) {

                // Go through...
                foreach ($plugins_list as $plugin) {

                    if (Filesystem::fileExists($_plugin_manifest = PLUGINS_PATH . '/' . $plugin . '/' . $plugin . '.yaml')) {
                        $plugin_manifest = Yaml::parseFile($_plugin_manifest);
                    }

                    $_plugins_config[basename($_plugin_manifest, '.yaml')] = $plugin_manifest;
                }

                Registry::set('plugins', $_plugins_config);
                Cache::driver()->save($plugins_cache_id, $_plugins_config);
            }
        }

        // Create dictionary
        if (is_array($plugins_list) && count($plugins_list) > 0) {
            foreach (static::$locales as $locale => $locale_title) {
                foreach ($plugins_list as $plugin) {
                    $language_file = PLUGINS_PATH . '/' . $plugin . '/languages/' . $locale . '.yaml';
                    if (Filesystem::fileExists($language_file)) {
                        I18n::add($plugin, $locale, Yaml::parseFile($language_file));
                    }
                }
            }
        }

        // Include enabled plugins
        if (is_array(Registry::get('plugins')) && count(Registry::get('plugins')) > 0) {
            foreach (Registry::get('plugins') as $plugin_name => $plugin) {
                if (Registry::get('plugins.'.$plugin_name.'.enabled')) {
                    include_once PLUGINS_PATH .'/'. $plugin_name .'/'. $plugin_name . '.php';
                }
            }
        }

        Event::dispatch('onPluginsInitialized');
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
