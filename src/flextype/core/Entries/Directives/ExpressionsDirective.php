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
use function Flextype\expression;
use function Flextype\registry;
use function Flextype\collection;

// Directive: [[  ]]
emitter()->addListener('onEntriesFetchSingleField', static function (): void {
    if (! registry()->get('flextype.settings.entries.directives.expressions.enabled')) {
        return;
    }

    if (! registry()->get('flextype.settings.entries.directives.expressions.enabled_globally')) {
        return;
    }

    $selfQuote  = fn ($text) => preg_replace('/(.)/us', '\\\\$0', $text);
    $openingTag = registry()->get('flextype.settings.entries.directives.expressions.opening_tag');
    $closingTag = registry()->get('flextype.settings.entries.directives.expressions.closing_tag');

    $field = entries()->registry()->get('methods.fetch.field');

    if (is_string($field['value'])) {
        $field['value'] = preg_replace_callback('/' . $selfQuote($openingTag) . ' (.*?) ' . $selfQuote($closingTag) . '/s', function ($matches) {

            // Prepare vars
            foreach (json_decode(json_encode((object) entries()->registry()->get('methods.fetch.result')), false) as $key => $value) {
                $vars[$key] = $value;
            }

            return expression()->evaluate($matches[1], $vars);
        }, $field['value']);
    }

    entries()->registry()->set('methods.fetch.field.key', $field['key']);
    entries()->registry()->set('methods.fetch.field.value', $field['value']);
});
