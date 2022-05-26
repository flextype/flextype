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

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

use function parsers;
use function registry;

// Shortcode: tr
// Usage: (tr:foo values='foo=Foo' locale:'en_US')
parsers()->shortcodes()->addHandler('tr', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.i18n.enabled')) {
        return '';
    }

    if ($s->getBbCode() != null) {
        $translate = parsers()->shortcodes()->parse($s->getBbCode());
        $values    = ($s->getParameter('values') != null) ? collectionFromQueryString(parsers()->shortcodes()->parse($s->getParameter('values')))->toArray() : [];
        $locale    = ($s->getParameter('locale') != null) ? parsers()->shortcodes()->parse($s->getParameter('locale')) : null;
    
        return __($translate, $values, $locale);
   }

    return '';
});
