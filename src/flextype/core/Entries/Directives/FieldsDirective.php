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

    if (! registry()->get('flextype.settings.entries.directives.fields.enabled')) {
        return;
    }
    
    $field = entries()->registry()->get('methods.fetch.field');
    $entry = entries()->registry()->get('methods.fetch.result');

    if (is_string($field)) {
        $field = preg_replace_callback('/@field\((.*?)\)/', function($matches) use ($entry) {
            return $entry[$matches[1]] ?? '';
        }, $field);
    }

    entries()->registry()->set('methods.fetch.field', $field);
});