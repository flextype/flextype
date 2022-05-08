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
use Ramsey\Uuid\Uuid;
use function app;
use function parsers;
use function registry;

// Shortcode: [uuid1]
parsers()->shortcodes()->addHandler('uuid1', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.uuid.enabled')) {
        return '';
    }

    return Uuid::uuid1()->toString();
});

// Shortcode: [uuid2]
parsers()->shortcodes()->addHandler('uuid2', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.uuid.enabled')) {
        return '';
    }

    return Uuid::uuid2()->toString();
});

// Shortcode: [uuid3]
parsers()->shortcodes()->addHandler('uuid3', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.uuid.enabled')) {
        return '';
    }

    return Uuid::uuid3()->toString();
});


// Shortcode: [uuid4]
parsers()->shortcodes()->addHandler('uuid4', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.uuid.enabled')) {
        return '';
    }

    return Uuid::uuid4()->toString();
});