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
use function Glowy\Strings\strings;

// Directive: @markdown
emitter()->addListener('onEntriesFetchSingleField', static function (): void {

    if (! registry()->get('flextype.settings.entries.directives.markdown.enabled')) {
        return;
    }

    // Save markdown cache state to restore it later
    $markdownCacheState = registry()->get('flextype.settings.parsers.markdown.cache');
    
    // Set markdown cache to false
    registry()->set('flextype.settings.parsers.markdown.cache', false);

    $field = entries()->registry()->get('methods.fetch.field');

    if (is_string($field['value'])) {
        if (strings($field['value'])->contains('@markdown')) {
            $field['value'] = strings(parsers()->markdown()->parse($field['value']))->replace('@markdown', '')->trim()->toString();
        } elseif (registry()->get('flextype.settings.entries.directives.markdown.enabled_globally')) {
            $field['value'] = parsers()->markdown()->parse($field['value']);
        }
    }
    
    entries()->registry()->set('methods.fetch.field.key', $field['key']);
    entries()->registry()->set('methods.fetch.field.value', $field['value']);

    // Restore markdown cache state
    registry()->set('flextype.settings.parsers.markdown.cache', $markdownCacheState);
});