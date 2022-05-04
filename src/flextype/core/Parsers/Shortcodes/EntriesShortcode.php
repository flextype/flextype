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
use Flextype\Entries\Entries;

use function arrays;
use function entries;
use function parsers;
use function registry;

// Shortcode: [entries-fetch id="STRING" field="STRING" default="STRING"]
parsers()->shortcodes()->addHandler('entries-fetch', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.entries.enabled')) {
        return '';
    }

    return collection(entries()->fetch($s->getParameter('id')))->get($s->getParameter('field'), $s->getParameter('default'));
});