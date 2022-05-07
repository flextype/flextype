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

    if (! registry()->get('flextype.settings.entries.directives.types.enabled')) {
        return;
    }
    
    $field = entries()->registry()->get('methods.fetch.field');

    strings($field)->contains('@type:integer') and $field = strings(strings($field)->replace('@type:integer', '')->trim())->toInteger();
    strings($field)->contains('@type:int') and $field = strings(strings($field)->replace('@type:int', '')->trim())->toInteger();
    strings($field)->contains('@type:float') and $field = strings(strings($field)->replace('@type:float', '')->trim())->toFloat();
    strings($field)->contains('@type:boolean') and $field = strings(strings($field)->replace('@type:boolean', '')->trim())->toBoolean();
    strings($field)->contains('@type:bool') and $field = strings(strings($field)->replace('@type:bool', '')->trim())->toBoolean();

    if (strings($field)->contains('@type:array')) {
        $field = strings($field)->replace('@type:array', '')->trim();
        if (strings($field)->isJson()) {
            $field = serializers()->json()->decode($field->toString());
        } else {
            $field = strings($field)->toArray(',');
        }
    }

    entries()->registry()->set('methods.fetch.field', $field);
});