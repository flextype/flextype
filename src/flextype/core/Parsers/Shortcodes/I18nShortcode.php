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
// Usage: (tr:foo)
parsers()->shortcodes()->addHandler('tr', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.i18n.enabled')) {
        return '';
    }

    $varsDelimeter = ($s->getParameter('varsDelimeter') != null) ? parsers()->shortcodes()->parse($s->getParameter('varsDelimeter')) : ',';

    if ($s->getBbCode() != null) {

        // Get vars
        foreach($s->getParameters() as $key => $value) {
            $vars = $value !== null ? strings($value)->contains($varsDelimeter) ? explode($varsDelimeter, $value) : [$value] : [];
        }
        
        // Parse shortcodes for each var.
        $vars = array_map(fn($v) => parsers()->shortcodes()->parse(is_string($v) ? $v : ''), $vars);

        return __($vars[0], collectionFromQueryString($vars[1] ?? '')->toArray(), $vars[2] ?? null);
   }

    return '';
});
