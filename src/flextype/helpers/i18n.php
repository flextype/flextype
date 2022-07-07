<?php

declare(strict_types=1);

namespace Flextype;

use function function_exists;

if (! function_exists('__')) {
    /**
     * Global Translation/Internationalization function.
     * Accepts an translation key and returns its translation for selected language.
     * If the given translation key is not available in the current dictionary the
     * translation key will be returned.
     *
     * Dislay a translated message for default locale
     * echo __('auth_login');
     *
     * // Display a translated message
     * echo __('auth_login', [], 'en_US');
     *
     * // With parameter replacement
     * echo __('auth_welcome_message', [':username' => $username], 'en_US');
     *
     * @param  string      $translate Translate to find
     * @param  array       $values    Values to replace in the translated text
     * @param  string|null $locale    Locale
     */
    function __(string $translate, array $values = [], string|null $locale = null): string
    {
        return I18n::find($translate, $values, $locale);
    }
}
