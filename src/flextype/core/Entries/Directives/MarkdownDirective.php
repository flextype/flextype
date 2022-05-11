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

    if (! registry()->get('flextype.settings.entries.directives.markdown.enabled')) {
        return;
    }

    $field = entries()->registry()->get('methods.fetch.field');

    if (is_string($field)) {
        if (strings($field)->contains('@markdown')) {
            $field = strings(parsers()->markdown()->parse($field))->replace('@markdown', '')->trim()->toString();
        }
    }
    
    entries()->registry()->set('methods.fetch.field', $field);
});