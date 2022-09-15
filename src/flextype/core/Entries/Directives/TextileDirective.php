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

namespace Flextype\Entries\Directives;

use function Flextype\emitter;
use function Flextype\entries;
use function Flextype\parsers;
use function Flextype\registry;
use function Glowy\Strings\strings;

// Directive: @textile
emitter()->addListener('onEntriesFetchSingleField', static function (): void {
    if (! registry()->get('flextype.settings.entries.directives.textile.enabled')) {
        return;
    }

    // Save textile cache state to restore it later
    $textileCacheState = registry()->get('flextype.settings.parsers.textile.cache');

    // Set textile cache to false
    registry()->set('flextype.settings.parsers.textile.cache', false);

    $field = entries()->registry()->get('methods.fetch.field');

    if (is_string($field['value']) && strings($field['value'])->contains('!textile')) {
        return;
    }

    if (is_string($field['value'])) {
        if (strings($field['value'])->contains('@textile')) {
            $field['value'] = strings(parsers()->textile()->parse($field['value']))->replace('@textile', '')->trim()->toString();
        } elseif (registry()->get('flextype.settings.entries.directives.textile.enabled_globally')) {
            $field['value'] = parsers()->textile()->parse($field['value']);
        }
    }

    entries()->registry()->set('methods.fetch.field.key', $field['key']);
    entries()->registry()->set('methods.fetch.field.value', $field['value']);

    // Restore textile cache state
    registry()->set('flextype.settings.parsers.textile.cache', $textileCacheState);
});
