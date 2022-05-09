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

    if (strings($field)->contains('@type:integer')) {
        $field = strings(strings($field)->replace('@type:integer', '')->trim())->toInteger();
    } elseif (strings($field)->contains('@type:int')) {
        $field = strings(strings($field)->replace('@type:int', '')->trim())->toInteger();
    } elseif (strings($field)->contains('@type:float')) {
        $field = strings(strings($field)->replace('@type:float', '')->trim())->toFloat();
    } elseif (strings($field)->contains('@type:boolean')) {
        $field = strings(strings($field)->replace('@type:boolean', '')->trim())->toBoolean();
    } elseif (strings($field)->contains('@type:bool')) {
        $field = strings(strings($field)->replace('@type:bool', '')->trim())->toBoolean();
    } elseif (strings($field)->contains('@type:array')) {
        $field = strings($field)->replace('@type:array', '')->trim();
        if (strings($field)->isJson()) {
            $field = serializers()->json()->decode($field->toString());
        } else {
            $field = strings($field)->toArray(',');
        }
    } elseif (strings($field)->contains('@type:collection')) {
        $field = strings($field)->replace('@type:collection', '')->trim();
        if (strings($field)->isJson()) {
            $field = collection(serializers()->json()->decode($field->toString()));
        } else {
            $field = collection(strings($field)->toArray(','));
        }
    } elseif (strings($field)->contains('@type:null')) {
        $field = null;
    }

    entries()->registry()->set('methods.fetch.field', $field);
});