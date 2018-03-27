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

class I18n
{
    /**
     * An instance of the I18n class
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
     * Dictionary
     *
     * @var array
     */
    public static $dictionary = [];

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access  protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

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
     * Init I18n
     *
     * @access protected
     * @return void
     */
    protected static function init() : void
    {

        // Get Plugins and Site Locales list
        (array) $plugins_list = Config::get('site.plugins');
        (array) $dictionary   = [];

        // Create dictionary
        if (is_array($plugins_list) && count($plugins_list) > 0) {
            foreach (static::$locales as $locale => $locale_title) {
                foreach ($plugins_list as $plugin) {
                    $language_file = PLUGINS_PATH . '/' . $plugin . '/languages/' . $locale . '.yml';
                    if (file_exists($language_file)) {
                        $dictionary[$plugin][$locale] = Yaml::parse(file_get_contents($language_file));
                    }
                }
            }
        }

        // Save dictionary
        static::$dictionary = $dictionary;
    }

    /**
     * Returns translation of a string. If no translation exists, the original
     * string will be returned. No parameters are replaced.
     *
     * @param  string $string    Text to translate
     * @param  string $namespace Namespace
     * @param  string $locale    Locale
     * @return string
     */
    public static function find(string $string, string $namespace, string $locale, array $values = []) : string
    {
        // Search current string to translate in the Dictionary
        if (isset(static::$dictionary[$namespace][$locale][$string])) {
            $string = static::$dictionary[$namespace][$locale][$string];
            $string = empty($values) ? $string : strtr($string, $values);
        } else {
            $string = $string;
        }

        // Return translation of a string
        return $string;
    }

    /**
     * Return the I18n instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new I18n();
    }
}
