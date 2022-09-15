<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS
 * and with the full functionality of a traditional CMS!
 *
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype;

use function strtr;

class I18n
{
    /**
     * Dictionary
     *
     * @var array
     */
    public static array $dictionary = [];

    /**
     * Default locale
     */
    public static string $locale = 'en_US';

    /**
     * Add translation keys
     *
     * New translation keus for default locale
     * I18n::add(['auth_login' => 'Login', 'auth_password' => 'Password']);
     *
     * New translation keys for `en_US` locale
     * I18n::add(['auth_login' => 'Login', 'auth_password' => 'Password'], 'en_US');
     *
     * @param  array       $translates Translation keys and values to add
     * @param  string|null $locale     Locale
     */
    public static function add(array $translates, string|null $locale = null): void
    {
        $locale ??= self::$locale;

        if (isset(self::$dictionary[$locale])) {
            self::$dictionary[$locale] += $translates;
        } else {
            self::$dictionary[$locale] = $translates;
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
     */
    public static function find(string $translate, array $values = [], ?string $locale = null): string
    {
        $locale ??= self::$locale;

        // Search current string to translate in the Dictionary
        if (isset(self::$dictionary[$locale][$translate])) {
            $translate = self::$dictionary[$locale][$translate];
            $translate = empty($values) ? $translate : strtr($translate, $values);
        } else {
            $translate = $translate;
        }

        // Return translation of a string
        return $translate;
    }
}
