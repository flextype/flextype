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

use function Flextype\parsers;
use function Flextype\registry;
use function ob_get_clean;
use function ob_start;

// Shortcode: php
// Usage: (php) php code here (/php)
//        (php) php code here
parsers()->shortcodes()->addHandler('php', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.php.enabled')) {
        return '';
    }

    if ($s->getContent() !== null) {
        ob_start();
        eval($s->getContent());

        return ob_get_clean();
    }

    return '@php';
});
