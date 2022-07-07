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

use function Flextype\emitter;
use function Flextype\entries;
use function Flextype\parsers;
use function Flextype\registry;
use function Glowy\Strings\strings;

// Directive: @shortcodes
emitter()->addListener('onEntriesFetchSingleField', static function (): void {
    if (! registry()->get('flextype.settings.entries.directives.shortcodes.enabled')) {
        return;
    }

    // Save shortcodes cache state to restore it later
    $shortcodesCacheState = registry()->get('flextype.settings.parsers.shortcodes.cache');

    // Set shortcodes cache to false
    registry()->set('flextype.settings.parsers.shortcodes.cache', false);

    $field = entries()->registry()->get('methods.fetch.field');

    if (is_string($field['value'])) {
        if (strings($field['value'])->contains('@shortcodes')) {
            $field['value'] = strings(parsers()->shortcodes()->parse($field['value']))->replace('@shortcodes', '')->trim()->toString();
        } elseif (registry()->get('flextype.settings.entries.directives.shortcodes.enabled_globally')) {
            $field['value'] = parsers()->shortcodes()->parse($field['value']);
        }
    }

    entries()->registry()->set('methods.fetch.field.key', $field['key']);
    entries()->registry()->set('methods.fetch.field.value', $field['value']);

    // Restore shortcodes cache state
    registry()->set('flextype.settings.parsers.shortcodes.cache', $shortcodesCacheState);
});
