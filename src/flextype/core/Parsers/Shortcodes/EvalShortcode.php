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

// Shortcode: eval
// Usage: (eval:2+2)
//        (eval)2+2(/eval)
//        (eval)registry.get('flextype.manifest.version')(/eval)
parsers()->shortcodes()->addHandler('eval', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.eval.enabled')) {
        return '';
    }

    if ($s->getContent() !== null) {
        return expression()->evaluate(parsers()->shortcodes()->parse($s->getContent()));
    }

    if ($s->getBbCode() !== null) {
        return expression()->evaluate(parsers()->shortcodes()->parse($s->getBBCode()));
    }
});
