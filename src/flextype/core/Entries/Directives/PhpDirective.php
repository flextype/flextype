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
use function Flextype\registry;
use function Glowy\Strings\strings;

// Directive: @php
emitter()->addListener('onEntriesFetchSingleField', static function (): void {
    if (! registry()->get('flextype.settings.entries.directives.php.enabled')) {
        return;
    }

    $field = entries()->registry()->get('methods.fetch.field');

    if (is_string($field['value'])) {
        if (strings($field['value'])->contains('@php')) {
            ob_start();
            eval(strings($field['value'])->replace('@php', '')->trim()->toString());
            $field['value'] = ob_get_clean();
        } elseif (registry()->get('flextype.settings.entries.directives.php.enabled_globally')) {
            ob_start();
            eval(strings($field['value'])->replace('@php', '')->trim()->toString());
            $field['value'] = ob_get_clean();
        }
    }

    entries()->registry()->set('methods.fetch.field.key', $field['key']);
    entries()->registry()->set('methods.fetch.field.value', $field['value']);
});
