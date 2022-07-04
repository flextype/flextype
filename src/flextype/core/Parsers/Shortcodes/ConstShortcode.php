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
use function Glowy\Registry\registry;

// Shortcode: const
// Usage: (const:PROJECT_DIR) 
parsers()->shortcodes()->addHandler('const', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.const.enabled')) {
        return '';
    }

    if ($s->getBBCode() !== null) {
        $const = parsers()->shortcodes()->parse($s->getBBCode());
        return defined($const) ? constant($const) : '';
    }

    return '';
});