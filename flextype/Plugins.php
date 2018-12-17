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
        'om' => 'Afaan Oromoo',
        'aa' => 'Afaraf',
        'af' => 'Afrikaans',
        'ak' => 'Akan',
        'an' => 'aragonés',
        'ig' => 'Asụsụ Igbo',
        'gn' => 'Avañe\'ẽ',
        'ae' => 'avesta',
        'ay' => 'aymar aru',
        'az' => 'azərbaycan dili',
        'id' => 'Bahasa Indonesia',
        'ms' => 'bahasa Melayu',
        'bm' => 'bamanankan',
        'jv' => 'basa Jawa',
        'su' => 'Basa Sunda',
        'bi' => 'Bislama',
        'bs' => 'bosanski jezik',
        'br' => 'brezhoneg',
        'ca' => 'català',
        'ch' => 'Chamoru',
        'ny' => 'chiCheŵa',
        'sn' => 'chiShona',
        'co' => 'corsu',
        'cy' => 'Cymraeg',
        'da' => 'dansk',
        'se' => 'Davvisámegiella',
        'de' => 'Deutsch',
        'nv' => 'Diné bizaad',
        'et' => 'eesti',
        'na' => 'Ekakairũ Naoero',
        'en' => 'English',
        'es' => 'español',
        'eo' => 'Esperanto',
        'eu' => 'euskara',
        'ee' => 'Eʋegbe',
        'to' => 'faka Tonga',
        'mg' => 'fiteny malagasy',
        'fr' => 'français',
        'fy' => 'Frysk',
        'ff' => 'Fulfulde',
        'fo' => 'føroyskt',
        'ga' => 'Gaeilge',
        'gv' => 'Gaelg',
        'sm' => 'gagana fa\'a Samoa',
        'gl' => 'galego',
        'sq' => 'gjuha shqipe',
        'gd' => 'Gàidhlig',
        'ki' => 'Gĩkũyũ',
        'ha' => 'Hausa',
        'ho' => 'Hiri Motu',
        'hr' => 'hrvatski jezik',
        'io' => 'Ido',
        'rw' => 'Ikinyarwanda',
        'rn' => 'Ikirundi',
        'ia' => 'Interlingua',
        'nd' => 'isiNdebele',
        'nr' => 'isiNdebele',
        'xh' => 'isiXhosa',
        'zu' => 'isiZulu',
        'it' => 'italiano',
        'ik' => 'Iñupiaq',
        'pl' => 'język polski',
        'mh' => 'Kajin M̧ajeļ',
        'kl' => 'kalaallisut',
        'kr' => 'Kanuri',
        'kw' => 'Kernewek',
        'kg' => 'KiKongo',
        'sw' => 'Kiswahili',
        'ht' => 'Kreyòl ayisyen',
        'kj' => 'Kuanyama',
        'ku' => 'Kurdî',
        'la' => 'latine',
        'lv' => 'latviešu valoda',
        'lt' => 'lietuvių kalba',
        'ro' => 'limba română',
        'li' => 'Limburgs',
        'ln' => 'Lingála',
        'lg' => 'Luganda',
        'lb' => 'Lëtzebuergesch',
        'hu' => 'magyar',
        'mt' => 'Malti',
        'nl' => 'Nederlands',
        'no' => 'Norsk',
        'nb' => 'Norsk bokmål',
        'nn' => 'Norsk nynorsk',
        'uz' => 'O\'zbek',
        'oc' => 'occitan',
        'ie' => 'Interlingue',
        'hz' => 'Otjiherero',
        'ng' => 'Owambo',
        'pt' => 'português',
        'ty' => 'Reo Tahiti',
        'rm' => 'rumantsch grischun',
        'qu' => 'Runa Simi',
        'sc' => 'sardu',
        'za' => 'Saɯ cueŋƅ',
        'st' => 'Sesotho',
        'tn' => 'Setswana',
        'ss' => 'SiSwati',
        'sl' => 'slovenski jezik',
        'sk' => 'slovenčina',
        'so' => 'Soomaaliga',
        'fi' => 'suomi',
        'sv' => 'Svenska',
        'mi' => 'te reo Māori',
        'vi' => 'Tiếng Việt',
        'lu' => 'Tshiluba',
        've' => 'Tshivenḓa',
        'tw' => 'Twi',
        'tk' => 'Türkmen',
        'tr' => 'Türkçe',
        'ug' => 'Uyƣurqə',
        'vo' => 'Volapük',
        'fj' => 'vosa Vakaviti',
        'wa' => 'walon',
        'tl' => 'Wikang Tagalog',
        'wo' => 'Wollof',
        'ts' => 'Xitsonga',
        'yo' => 'Yorùbá',
        'sg' => 'yângâ tî sängö',
        'is' => 'Íslenska',
        'cs' => 'čeština',
        'el' => 'ελληνικά',
        'av' => 'авар мацӀ',
        'ab' => 'аҧсуа бызшәа',
        'ba' => 'башҡорт теле',
        'be' => 'беларуская мова',
        'bg' => 'български език',
        'os' => 'ирон æвзаг',
        'kv' => 'коми кыв',
        'ky' => 'Кыргызча',
        'mk' => 'македонски јазик',
        'mn' => 'монгол',
        'ce' => 'нохчийн мотт',
        'ru' => 'Русский язык',
        'sr' => 'српски језик',
        'tt' => 'татар теле',
        'tg' => 'тоҷикӣ',
        'uk' => 'Українська',
        'cv' => 'чӑваш чӗлхи',
        'cu' => 'ѩзыкъ словѣньскъ',
        'kk' => 'қазақ тілі',
        'hy' => 'Հայերեն',
        'yi' => 'ייִדיש',
        'he' => 'עברית',
        'ur' => 'اردو',
        'ar' => 'العربية',
        'fa' => 'فارسی',
        'ps' => 'پښتو',
        'ks' => 'कश्मीरी',
        'ne' => 'नेपाली',
        'pi' => 'पाऴि',
        'bh' => 'भोजपुरी',
        'mr' => 'मराठी',
        'sa' => 'संस्कृतम्',
        'sd' => 'सिन्धी',
        'hi' => 'हिन्दी',
        'as' => 'অসমীয়া',
        'bn' => 'বাংলা',
        'pa' => 'ਪੰਜਾਬੀ',
        'gu' => 'ગુજરાતી',
        'or' => 'ଓଡ଼ିଆ',
        'ta' => 'தமிழ்',
        'te' => 'తెలుగు',
        'kn' => 'ಕನ್ನಡ',
        'ml' => 'മലയാളം',
        'si' => 'සිංහල',
        'th' => 'ไทย',
        'lo' => 'ພາສາລາວ',
        'bo' => 'བོད་ཡིག',
        'dz' => 'རྫོང་ཁ',
        'my' => 'ဗမာစာ',
        'ka' => 'ქართული',
        'ti' => 'ትግርኛ',
        'am' => 'አማርኛ',
        'iu' => 'ᐃᓄᒃᑎᑐᑦ',
        'oj' => 'ᐊᓂᔑᓈᐯᒧᐎᓐ',
        'cr' => 'ᓀᐦᐃᔭᐍᐏᐣ',
        'km' => 'ខ្មែរ',
        'zh' => '中文 (Zhōngwén)',
        'ja' => '日本語 (にほんご)',
        'ii' => 'ꆈꌠ꒿ Nuosuhxop',
        'ko' => '한국어 (韓國語)'
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
        $plugins_list = Filesystem::getDirList(PATH['plugins']);

        // If Plugins List isnt empty then create plugin cache ID
        if (is_array($plugins_list) && count($plugins_list) > 0) {

            // Go through...
            foreach ($plugins_list as $plugin) {
                if (Filesystem::fileExists($_plugin_settings = PATH['plugins'] . '/' . $plugin . '/settings.yaml') and
                    Filesystem::fileExists($_plugin_config = PATH['plugins'] . '/' . $plugin . '/'. $plugin .'.yaml')) {
                    $_plugins_cache_id .= filemtime($_plugin_settings) . filemtime($_plugin_config);
                }
            }

            // Create Unique Cache ID for Plugins
            $plugins_cache_id = md5('plugins' . PATH['plugins'] . '/'  . $_plugins_cache_id);

            // Get plugins list from cache or scan plugins folder and create new plugins cache item
            if (Cache::contains($plugins_cache_id)) {
                Registry::set('plugins', Cache::fetch($plugins_cache_id));
            } else {

                // If Plugins List isnt empty
                if (is_array($plugins_list) && count($plugins_list) > 0) {

                    // Go through...
                    foreach ($plugins_list as $plugin) {
                        if (Filesystem::fileExists($_plugin_settings = PATH['plugins'] . '/' . $plugin . '/settings.yaml')) {
                            $plugin_settings = YamlParser::decode(Filesystem::getFileContent($_plugin_settings));
                        }

                        if (Filesystem::fileExists($_plugin_config = PATH['plugins'] . '/' . $plugin . '/'. $plugin. '.yaml')) {
                            $plugin_config = YamlParser::decode(Filesystem::getFileContent($_plugin_config));
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
                        $language_file = PATH['plugins'] . '/' . $plugin . '/languages/' . $locale . '.yaml';
                        if (Filesystem::fileExists($language_file)) {
                            I18n::add(YamlParser::decode(Filesystem::getFileContent($language_file)), $locale);
                        }
                    }
                }
            }

            // Include enabled plugins
            if (is_array(Registry::get('plugins')) && count(Registry::get('plugins')) > 0) {
                foreach (Registry::get('plugins') as $plugin_name => $plugin) {
                    if (Registry::get('plugins.'.$plugin_name.'.enabled')) {
                        include_once PATH['plugins'] . '/' . $plugin_name .'/'. $plugin_name . '.php';
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
