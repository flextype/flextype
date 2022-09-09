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

use function Flextype\expression;
use function Flextype\parsers;
use function Flextype\registry;

// Shortcode: if
// Usage: (if:'(var:score) < (var:level1)') Show something... (/if)
parsers()->shortcodes()->addHandler('if', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.if.enabled')) {
        return '';
    }

    return parsers()->expressions()->eval(parsers()->shortcodes()->parse(($s->getBbCode() ?? ''))) === true ? parsers()->shortcodes()->parse($s->getContent()) : '';
});
