<?php
namespace Rawilum;

use Symfony\Component\Yaml\Yaml;

/**
 * This file is part of the Rawilum.
 *
 * (c) Romanenko Sergey / Awilum <awilum@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class I18n
{
    /**
     * Locales array
     *
     * @var array
     */
    public $locales = array(
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
    );

    /**
     * @var Rawilum
     */
    protected $rawilum;

    /**
     * Dictionary
     *
     * @var array
     */
    public $dictionary = array();

    /**
     * Construct
     */
    public function __construct(Rawilum $c)
    {
        $this->rawilum = $c;
    }

    /**
     * Init
     */
    public function init()
    {

        // Get Plugins and Site Locales list
        (array) $plugins_list = $this->rawilum['config']->get('site.plugins');
        (array) $locales = $this->rawilum['config']->get('site.locales');
        (array) $dictionary = [];

        // Create dictionary
        if (is_array($plugins_list) && count($plugins_list) > 0) {
            foreach ($locales as $locale) {
                foreach ($plugins_list as $plugin) {
                    $language_file = PLUGINS_PATH . '/' . $plugin . '/languages/' . $locale . '.yml';
                    if (file_exists($language_file)) {
                        $dictionary[$plugin][$locale] = Yaml::parse(file_get_contents($language_file));
                    }
                }
            }
        }

        // Save dictionary
        $this->dictionary = $dictionary;
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
    public function find(string $string, string $namespace, string $locale, array $values = []) : string
    {
        // Search current string to translate in the Dictionary
        if (isset($this->dictionary[$namespace][$locale][$string])) {
            $string = $this->dictionary[$namespace][$locale][$string];
            $string = empty($values) ? $string : strtr($string, $values);
        } else {
            $string = $string;
        }

        // Return translation of a string
        return $string;
    }
}
