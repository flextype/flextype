<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

class I18n
{
    /**
     * Dictionary
     *
     * @var array
     */
    public static $dictionary = [];

    /**
     * Default locale
     *
     * @var string
     */
    public static $locale = 'en_US';

    /**
     * Add translation keys
     *
     * New translation keus for default locale
     * I18n::add(['auth_login' => 'Login', 'auth_password' => 'Password']);
     *
     * New translation keys for `en_US` locale
     * I18n::add(['auth_login' => 'Login', 'auth_password' => 'Password'], 'en_US');
     *
     * @param  string $translates Translation keys and values to add
     * @param  string $locale     Locale
     * @return void
     */
    public static function add(array $translates, string $locale = null) : void
    {
        $locale = ($locale === null) ? I18n::$locale : $locale;

        if (isset(I18n::$dictionary[$locale])) {
            I18n::$dictionary[$locale] += $translates;
        } else {
            I18n::$dictionary[$locale] = $translates;
        }
    }

    /**
     * Returns translation of a string. If no translation exists, the original
     * string will be returned. No parameters are replaced.
     *
     * Get translated string for `auth_login` for default locale
     * $translated_string = I18n::find('auth_login');
     *
     * @param  string $translate Translate to find
     * @param  array  $values    Values to replace in the translated text
     * @param  string $locale    Locale
     * @return string
     */
    public static function find(string $translate, array $values = [], string $locale = null) : string
    {
        $locale = ($locale === null) ? I18n::$locale : $locale;

        // Search current string to translate in the Dictionary
        if (isset(I18n::$dictionary[$locale][$translate])) {
            $translate = I18n::$dictionary[$locale][$translate];
            $translate = empty($values) ? $translate : strtr($translate, $values);
        } else {
            $translate = $translate;
        }

        // Return translation of a string
        return $translate;
    }
}