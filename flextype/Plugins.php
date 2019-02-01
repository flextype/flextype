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

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Event\Event;
use Flextype\Component\I18n\I18n;
use Flextype\Component\Registry\Registry;

class Plugins
{
    /**
     * An instance of the Cache class
     *
     * @var object
     * @access private
     */
    private static $instance = null;

    /**
     * Locales array
     *
     * @var array
     */
    private static $locales = [
        'af'         => ['name' => 'Afrikaans', 'nativeName' => 'Afrikaans'],
        'af_ZA'      => ['name' => 'Afrikaans', 'nativeName' => 'Afrikaans'],
        'ak'         => ['name' => 'Akan', 'nativeName' => 'Akan'], // unverified native name
        'ast'        => ['name' => 'Asturian', 'nativeName' => 'Asturianu'],
        'ar'         => ['name' => 'Arabic', 'nativeName' => 'عربي', 'orientation' => 'rtl'],
        'ar_SA'      => ['name' => 'Arabic', 'nativeName' => 'عربي', 'orientation' => 'rtl'],
        'as'         => ['name' => 'Assamese', 'nativeName' => 'অসমীয়া'],
        'be'         => ['name' => 'Belarusian', 'nativeName' => 'Беларуская'],
        'bg'         => ['name' => 'Bulgarian', 'nativeName' => 'Български'],
        'bn'         => ['name' => 'Bengali', 'nativeName' => 'বাংলা'],
        'bn_BD'      => ['name' => 'Bengali (Bangladesh)', 'nativeName' => 'বাংলা (বাংলাদেশ)'],
        'bn_IN'      => ['name' => 'Bengali (India)', 'nativeName' => 'বাংলা (ভারত)'],
        'br'         => ['name' => 'Breton', 'nativeName' => 'Brezhoneg'],
        'bs'         => ['name' => 'Bosnian', 'nativeName' => 'Bosanski'],
        'ca'         => ['name' => 'Catalan', 'nativeName' => 'Català'],
        'ca_ES'      => ['name' => 'Catalan', 'nativeName' => 'Català'],
        'ca_valencia'=> ['name' => 'Catalan (Valencian)', 'nativeName' => 'Català (valencià)'], // not iso-639-1. a=l10n-drivers
        'cs'         => ['name' => 'Czech', 'nativeName' => 'Čeština'],
        'cs_CZ'      => ['name' => 'Czech', 'nativeName' => 'Čeština'],
        'cy'         => ['name' => 'Welsh', 'nativeName' => 'Cymraeg'],
        'da'         => ['name' => 'Danish', 'nativeName' => 'Dansk'],
        'da_DK'      => ['name' => 'Danish', 'nativeName' => 'Dansk'],
        'de'         => ['name' => 'German', 'nativeName' => 'Deutsch'],
        'de_AT'      => ['name' => 'German (Austria)', 'nativeName' => 'Deutsch (Österreich)'],
        'de_CH'      => ['name' => 'German (Switzerland)', 'nativeName' => 'Deutsch (Schweiz)'],
        'de_DE'      => ['name' => 'German (Germany)', 'nativeName' => 'Deutsch (Deutschland)'],
        'dsb'        => ['name' => 'Lower Sorbian', 'nativeName' => 'Dolnoserbšćina'], // iso-639-2
        'el'         => ['name' => 'Greek', 'nativeName' => 'Ελληνικά'],
        'el_GR'      => ['name' => 'Greek', 'nativeName' => 'Ελληνικά'],
        'en'         => ['name' => 'English', 'nativeName' => 'English'],
        'en_AU'      => ['name' => 'English (Australian)', 'nativeName' => 'English (Australian)'],
        'en_CA'      => ['name' => 'English (Canadian)', 'nativeName' => 'English (Canadian)'],
        'en_GB'      => ['name' => 'English (British)', 'nativeName' => 'English (British)'],
        'en_NZ'      => ['name' => 'English (New Zealand)', 'nativeName' => 'English (New Zealand)'],
        'en_US'      => ['name' => 'English (US)', 'nativeName' => 'English (US)'],
        'en_ZA'      => ['name' => 'English (South African)', 'nativeName' => 'English (South African)'],
        'eo'         => ['name' => 'Esperanto', 'nativeName' => 'Esperanto'],
        'es'         => ['name' => 'Spanish', 'nativeName' => 'Español'],
        'es_AR'      => ['name' => 'Spanish (Argentina)', 'nativeName' => 'Español (de Argentina)'],
        'es_CL'      => ['name' => 'Spanish (Chile)', 'nativeName' => 'Español (de Chile)'],
        'es_ES'      => ['name' => 'Spanish (Spain)', 'nativeName' => 'Español (de España)'],
        'es_MX'      => ['name' => 'Spanish (Mexico)', 'nativeName' => 'Español (de México)'],
        'et'         => ['name' => 'Estonian', 'nativeName' => 'Eesti keel'],
        'eu'         => ['name' => 'Basque', 'nativeName' => 'Euskara'],
        'fa'         => ['name' => 'Persian', 'nativeName' => 'فارسی', 'orientation' => 'rtl'],
        'fi'         => ['name' => 'Finnish', 'nativeName' => 'Suomi'],
        'fi_FI'      => ['name' => 'Finnish', 'nativeName' => 'Suomi'],
        'fj_FJ'      => ['name' => 'Fijian', 'nativeName' => 'Vosa vaka_Viti'],
        'fr'         => ['name' => 'French', 'nativeName' => 'Français'],
        'fr_CA'      => ['name' => 'French (Canada)', 'nativeName' => 'Français (Canada)'],
        'fr_FR'      => ['name' => 'French (France)', 'nativeName' => 'Français (France)'],
        'fur'        => ['name' => 'Friulian', 'nativeName' => 'Furlan'],
        'fur_IT'     => ['name' => 'Friulian', 'nativeName' => 'Furlan'],
        'fy'         => ['name' => 'Frisian', 'nativeName' => 'Frysk'],
        'fy_NL'      => ['name' => 'Frisian', 'nativeName' => 'Frysk'],
        'ga'         => ['name' => 'Irish', 'nativeName' => 'Gaeilge'],
        'ga_IE'      => ['name' => 'Irish (Ireland)', 'nativeName' => 'Gaeilge (Éire)'],
        'gd'         => ['name' => 'Gaelic (Scotland)', 'nativeName' => 'Gàidhlig'],
        'gl'         => ['name' => 'Galician', 'nativeName' => 'Galego'],
        'gu'         => ['name' => 'Gujarati', 'nativeName' => 'ગુજરાતી'],
        'gu_IN'      => ['name' => 'Gujarati', 'nativeName' => 'ગુજરાતી'],
        'he'         => ['name' => 'Hebrew', 'nativeName' => 'עברית', 'orientation' => 'rtl'],
        'he_IL'      => ['name' => 'Hebrew', 'nativeName' => 'עברית', 'orientation' => 'rtl'],
        'hi'         => ['name' => 'Hindi', 'nativeName' => 'हिन्दी'],
        'hi_IN'      => ['name' => 'Hindi (India)', 'nativeName' => 'हिन्दी (भारत)'],
        'hr'         => ['name' => 'Croatian', 'nativeName' => 'Hrvatski'],
        'hr_HR'      => ['name' => 'Croatian', 'nativeName' => 'Hrvatski'],
        'hsb'        => ['name' => 'Upper Sorbian', 'nativeName' => 'Hornjoserbsce'],
        'hu'         => ['name' => 'Hungarian', 'nativeName' => 'Magyar'],
        'hu_HU'      => ['name' => 'Hungarian', 'nativeName' => 'Magyar'],
        'hy'         => ['name' => 'Armenian', 'nativeName' => 'Հայերեն'],
        'hy_AM'      => ['name' => 'Armenian', 'nativeName' => 'Հայերեն'],
        'id'         => ['name' => 'Indonesian', 'nativeName' => 'Bahasa Indonesia'],
        'is'         => ['name' => 'Icelandic', 'nativeName' => 'íslenska'],
        'it'         => ['name' => 'Italian', 'nativeName' => 'Italiano'],
        'it_IT'      => ['name' => 'Italian', 'nativeName' => 'Italiano'],
        'ja'         => ['name' => 'Japanese', 'nativeName' => '日本語'],
        'ja_JP'      => ['name' => 'Japanese', 'nativeName' => '日本語'], // not iso-639-1
        'ka'         => ['name' => 'Georgian', 'nativeName' => 'ქართული'],
        'kk'         => ['name' => 'Kazakh', 'nativeName' => 'Қазақ'],
        'kn'         => ['name' => 'Kannada', 'nativeName' => 'ಕನ್ನಡ'],
        'ko'         => ['name' => 'Korean', 'nativeName' => '한국어'],
        'ko_KR'      => ['name' => 'Korean', 'nativeName' => '한국어'],
        'ku'         => ['name' => 'Kurdish', 'nativeName' => 'Kurdî'],
        'la'         => ['name' => 'Latin', 'nativeName' => 'Latina'],
        'lb'         => ['name' => 'Luxembourgish', 'nativeName' => 'Lëtzebuergesch'],
        'lg'         => ['name' => 'Luganda', 'nativeName' => 'Luganda'],
        'lt'         => ['name' => 'Lithuanian', 'nativeName' => 'Lietuvių kalba'],
        'lv'         => ['name' => 'Latvian', 'nativeName' => 'Latviešu'],
        'mai'        => ['name' => 'Maithili', 'nativeName' => 'मैथिली মৈথিলী'],
        'mg'         => ['name' => 'Malagasy', 'nativeName' => 'Malagasy'],
        'mi'         => ['name' => 'Maori (Aotearoa)', 'nativeName' => 'Māori (Aotearoa)'],
        'mk'         => ['name' => 'Macedonian', 'nativeName' => 'Македонски'],
        'ml'         => ['name' => 'Malayalam', 'nativeName' => 'മലയാളം'],
        'mn'         => ['name' => 'Mongolian', 'nativeName' => 'Монгол'],
        'mr'         => ['name' => 'Marathi', 'nativeName' => 'मराठी'],
        'no'         => ['name' => 'Norwegian', 'nativeName' => 'Norsk'],
        'no_NO'      => ['name' => 'Norwegian', 'nativeName' => 'Norsk'],
        'nb'         => ['name' => 'Norwegian', 'nativeName' => 'Norsk'],
        'nb_NO'      => ['name' => 'Norwegian (Bokmål)', 'nativeName' => 'Norsk bokmål'],
        'ne_NP'      => ['name' => 'Nepali', 'nativeName' => 'नेपाली'],
        'nn_NO'      => ['name' => 'Norwegian (Nynorsk)', 'nativeName' => 'Norsk nynorsk'],
        'nl'         => ['name' => 'Dutch', 'nativeName' => 'Nederlands'],
        'nl_NL'      => ['name' => 'Dutch', 'nativeName' => 'Nederlands'],
        'nr'         => ['name' => 'Ndebele, South', 'nativeName' => 'IsiNdebele'],
        'nso'        => ['name' => 'Northern Sotho', 'nativeName' => 'Sepedi'],
        'oc'         => ['name' => 'Occitan (Lengadocian)', 'nativeName' => 'Occitan (lengadocian)'],
        'or'         => ['name' => 'Oriya', 'nativeName' => 'ଓଡ଼ିଆ'],
        'pa'         => ['name' => 'Punjabi', 'nativeName' => 'ਪੰਜਾਬੀ'],
        'pa_IN'      => ['name' => 'Punjabi', 'nativeName' => 'ਪੰਜਾਬੀ'],
        'pl'         => ['name' => 'Polish', 'nativeName' => 'Polski'],
        'pl_PL'      => ['name' => 'Polish', 'nativeName' => 'Polski'],
        'pt'         => ['name' => 'Portuguese', 'nativeName' => 'Português'],
        'pt_BR'      => ['name' => 'Portuguese (Brazilian)', 'nativeName' => 'Português (do Brasil)'],
        'pt_PT'      => ['name' => 'Portuguese (Portugal)', 'nativeName' => 'Português (Europeu)'],
        'ro'         => ['name' => 'Romanian', 'nativeName' => 'Română'],
        'ro_RO'      => ['name' => 'Romanian', 'nativeName' => 'Română'],
        'rm'         => ['name' => 'Romansh', 'nativeName' => 'Rumantsch'],
        'ru'         => ['name' => 'Russian', 'nativeName' => 'Русский'],
        'ru_RU'      => ['name' => 'Russian', 'nativeName' => 'Русский'],
        'rw'         => ['name' => 'Kinyarwanda', 'nativeName' => 'Ikinyarwanda'],
        'si'         => ['name' => 'Sinhala', 'nativeName' => 'සිංහල'],
        'sk'         => ['name' => 'Slovak', 'nativeName' => 'Slovenčina'],
        'sl'         => ['name' => 'Slovenian', 'nativeName' => 'Slovensko'],
        'son'        => ['name' => 'Songhai', 'nativeName' => 'Soŋay'],
        'sq'         => ['name' => 'Albanian', 'nativeName' => 'Shqip'],
        'sr'         => ['name' => 'Serbian', 'nativeName' => 'Српски'],
        'sr_SP'      => ['name' => 'Serbian', 'nativeName' => 'Српски'],
        'sr_Latn'    => ['name' => 'Serbian', 'nativeName' => 'Srpski'], // follows RFC 4646
        'ss'         => ['name' => 'Siswati', 'nativeName' => 'siSwati'],
        'st'         => ['name' => 'Southern Sotho', 'nativeName' => 'Sesotho'],
        'sv'         => ['name' => 'Swedish', 'nativeName' => 'Svenska'],
        'sv_SE'      => ['name' => 'Swedish', 'nativeName' => 'Svenska'],
        'ta'         => ['name' => 'Tamil', 'nativeName' => 'தமிழ்'],
        'ta_IN'      => ['name' => 'Tamil (India)', 'nativeName' => 'தமிழ் (இந்தியா)'],
        'ta_LK'      => ['name' => 'Tamil (Sri Lanka)', 'nativeName' => 'தமிழ் (இலங்கை)'],
        'te'         => ['name' => 'Telugu', 'nativeName' => 'తెలుగు'],
        'th'         => ['name' => 'Thai', 'nativeName' => 'ไทย'],
        'tlh'        => ['name' => 'Klingon', 'nativeName' => 'Klingon'],
        'tn'         => ['name' => 'Tswana', 'nativeName' => 'Setswana'],
        'tr'         => ['name' => 'Turkish', 'nativeName' => 'Türkçe'],
        'tr_TR'      => ['name' => 'Turkish', 'nativeName' => 'Türkçe'],
        'ts'         => ['name' => 'Tsonga', 'nativeName' => 'Xitsonga'],
        'tt'         => ['name' => 'Tatar', 'nativeName' => 'Tatarça'],
        'tt_RU'      => ['name' => 'Tatar', 'nativeName' => 'Tatarça'],
        'uk'         => ['name' => 'Ukrainian', 'nativeName' => 'Українська'],
        'uk_UA'      => ['name' => 'Ukrainian', 'nativeName' => 'Українська'],
        'ur'         => ['name' => 'Urdu', 'nativeName' => 'اُردو', 'orientation' => 'rtl'],
        've'         => ['name' => 'Venda', 'nativeName' => 'Tshivenḓa'],
        'vi'         => ['name' => 'Vietnamese', 'nativeName' => 'Tiếng Việt'],
        'vi_VN'      => ['name' => 'Vietnamese', 'nativeName' => 'Tiếng Việt'],
        'wo'         => ['name' => 'Wolof', 'nativeName' => 'Wolof'],
        'xh'         => ['name' => 'Xhosa', 'nativeName' => 'isiXhosa'],
        'zh'         => ['name' => 'Chinese (Simplified)', 'nativeName' => '中文 (简体)'],
        'zh_CN'      => ['name' => 'Chinese (Simplified)', 'nativeName' => '中文 (简体)'],
        'zh_TW'      => ['name' => 'Chinese (Traditional)', 'nativeName' => '正體中文 (繁體)'],
        'zu'         => ['name' => 'Zulu', 'nativeName' => 'isiZulu']
    ];

    /**
     * Private clone method to enforce singleton behavior.
     *
     * @access private
     */
    private function __clone()
    {
    }

    /**
     * Private wakeup method to enforce singleton behavior.
     *
     * @access private
     */
    private function __wakeup()
    {
    }

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    private function __construct()
    {
        Plugins::init();
    }

    /**
     * Init Plugins
     *
     * @access private
     * @return void
     */
    private static function init() : void
    {
        // Plugin cache id
        $plugins_cache_id = '';
        $_plugins_cache_id = '';

        // Set empty plugins item
        Registry::set('plugins', []);

        // Get Plugins List
        $plugins_list = Filesystem::listContents(PATH['plugins']);

        // If Plugins List isnt empty then create plugin cache ID
        if (is_array($plugins_list) && count($plugins_list) > 0) {

            // Go through...
            foreach ($plugins_list as $plugin) {
                if (Filesystem::has($_plugin_settings = PATH['plugins'] . '/' . $plugin['dirname'] . '/settings.yaml') and
                    Filesystem::has($_plugin_config = PATH['plugins'] . '/' . $plugin['dirname'] . '/' . $plugin['dirname'] . '.yaml')) {
                    $_plugins_cache_id .= filemtime($_plugin_settings) . filemtime($_plugin_config);
                }
            }

            // Create Unique Cache ID for Plugins
            $plugins_cache_id = md5('plugins' . PATH['plugins'] . '/' . $_plugins_cache_id);

            // Get plugins list from cache or scan plugins folder and create new plugins cache item
            if (Cache::contains($plugins_cache_id)) {
                Registry::set('plugins', Cache::fetch($plugins_cache_id));
            } else {

                // If Plugins List isnt empty
                if (is_array($plugins_list) && count($plugins_list) > 0) {

                    // Go through...
                    foreach ($plugins_list as $plugin) {
                        if (Filesystem::has($_plugin_settings = PATH['plugins'] . '/' . $plugin['dirname'] . '/settings.yaml')) {
                            $plugin_settings = YamlParser::decode(Filesystem::read($_plugin_settings));
                        }

                        if (Filesystem::has($_plugin_config = PATH['plugins'] . '/' . $plugin['dirname'] . '/' . $plugin['dirname'] . '.yaml')) {
                            $plugin_config = YamlParser::decode(Filesystem::read($_plugin_config));
                        }

                        $_plugins_config[basename($_plugin_config, '.yaml')] = array_merge($plugin_settings, $plugin_config);
                    }

                    Registry::set('plugins', $_plugins_config);
                    Cache::save($plugins_cache_id, $_plugins_config);
                }
            }

            // Create Dictionary
            if (is_array($plugins_list) && count($plugins_list) > 0) {
                foreach (Plugins::$locales as $locale => $locale_title) {
                    foreach ($plugins_list as $plugin) {
                        $language_file = PATH['plugins'] . '/' . $plugin['dirname'] . '/languages/' . $locale . '.yaml';
                        if (Filesystem::has($language_file)) {
                            I18n::add(YamlParser::decode(Filesystem::read($language_file)), $locale);
                        }
                    }
                }
            }

            // Include enabled plugins
            if (is_array(Registry::get('plugins')) && count(Registry::get('plugins')) > 0) {
                foreach (Registry::get('plugins') as $plugin_name => $plugin) {
                    if (Registry::get('plugins.' . $plugin_name . '.enabled')) {
                        include_once PATH['plugins'] . '/' . $plugin_name . '/' . $plugin_name . '.php';
                    }
                }
            }

            Event::dispatch('onPluginsInitialized');
        }
    }

    /**
     * Get locales.
     *
     * @access public
     * @return array
     */
    public static function getLocales() : array
    {
        return Plugins::$locales;
    }

    /**
     * Get the Plugins instance.
     *
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (is_null(Plugins::$instance)) {
            Plugins::$instance = new self;
        }

        return Plugins::$instance;
    }
}
