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

// Shortcode: [tr]
parsers()->shortcodes()->addHandler('tr', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.i18n.enabled')) {
        return '';
    }

    $varsDelimeter = $s->getParameter('varsDelimeter') ?: ',';

    if ($s->getParameter('find') != null) {

        // Get vars
        foreach($s->getParameters() as $key => $value) {
            $vars = $value !== null ? strings($value)->contains($varsDelimeter) ? explode($varsDelimeter, $value) : [$value] : [];
        }
        
        return __($vars[0], collectionFromQueryString($vars[1] ?? '')->toArray(), $vars[2] ?? null);
   }

    return '';
});
