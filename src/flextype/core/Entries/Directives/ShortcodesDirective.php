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

use Glowy\Arrays\Arrays as Collection;

emitter()->addListener('onEntriesFetchSingleDirectives', static function (): void {

    if (! registry()->get('flextype.settings.entries.directives.shortcodes.enabled')) {
        return;
    }

    $field = entries()->registry()->get('methods.fetch.field');

    if (strings($field)->contains('@parser:shortcodes')) {
        $field = strings(parsers()->shortcodes()->parse($field))->replace('@parser:shortcodes', '')->trim()->toString();
    } elseif (registry()->get('flextype.settings.entries.parsers.shortcodes.enabled') !== false) {
        $field = parsers()->shortcodes()->parse($field);
    }

    entries()->registry()->set('methods.fetch.field', $field);
});