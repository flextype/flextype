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

// Shortcode: unless
// Usage: (unless:'(var:score) < (var:level1)') Show something... (/when)
parsers()->shortcodes()->addHandler('unless', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.unless.enabled')) {
        return '';
    }

    return expression()->evaluate(parsers()->shortcodes()->parse(($s->getBbCode() ?? ''))) === false ? parsers()->shortcodes()->parse($s->getContent()) : '';
});
