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

use function Glowy\Strings\strings;
use function Flextype\emitter;
use function Flextype\entries;
use function Flextype\parsers;
use function Flextype\registry;
use function Flextype\collection;

// Directive: [[ ]] [% %] [# #]
emitter()->addListener('onEntriesFetchSingleField', static function (): void {

    if (! registry()->get('flextype.settings.entries.directives.expressions.enabled')) {
        return;
    }

    if (! registry()->get('flextype.settings.entries.directives.expressions.enabled_globally')) {
        return;
    }

    $field = entries()->registry()->get('methods.fetch.field');

    if (is_string($field['value']) && strings($field['value'])->contains('!expressions')) {
        return;
    }

    $vars = [];
        
    // Convert entry fields to vars.
    foreach (json_decode(json_encode((object) entries()->registry()->get('methods.fetch.result')), false) as $key => $value) {
        $vars[$key] = $value;
    }
    
    if (is_string($field['value'])) {
        $field['value'] = parsers()->expressions()->parse($field['value'], $vars);
    }

    entries()->registry()->set('methods.fetch.field.key', $field['key']);
    entries()->registry()->set('methods.fetch.field.value', $field['value']);
});
