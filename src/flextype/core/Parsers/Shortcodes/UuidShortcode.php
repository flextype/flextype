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
use function parsers;
use function Glowy\Registry\registry;
use function Glowy\Strings\strings;

// Shortcode: uuid
// Usage: (uuid) (uuid:4)
parsers()->shortcodes()->addHandler('uuid', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.uuid.enabled')) {
        return '';
    }

    $result = '';
    $uuid   = ($s->getBbCode() != null) ? strings(parsers()->shortcodes()->parse($s->getBbCode()))->toInteger() : 4;
    
    switch ($uuid) {
        case 4:
        default:
            $result = Uuid::uuid4()->toString();
            break;
    }

    return $result;
});