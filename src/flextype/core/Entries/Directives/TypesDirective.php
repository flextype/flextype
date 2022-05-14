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

// Directive: @type()
emitter()->addListener('onEntriesFetchSingleField', static function (): void {

    if (! registry()->get('flextype.settings.entries.directives.types.enabled')) {
        return;
    }
    
    $field = entries()->registry()->get('methods.fetch.field');

    if (is_string($field['value'])) {
        if (strings($field['value'])->contains('@type(integer)')) {
            $field['value'] = strings(strings($field['value'])->replace('@type(integer)', '')->trim())->toInteger();
        } elseif (strings($field['value'])->contains('@type(int)')) {
            $field['value'] = strings(strings($field['value'])->replace('@type(int)', '')->trim())->toInteger();
        } elseif (strings($field['value'])->contains('@type(float)')) {
            $field['value'] = strings(strings($field['value'])->replace('@type(float)', '')->trim())->toFloat();
        } elseif (strings($field['value'])->contains('@type(boolean)')) {
            $field['value'] = strings(strings($field['value'])->replace('@type(boolean)', '')->trim())->toBoolean();
        } elseif (strings($field['value'])->contains('@type(bool)')) {
            $field['value'] = strings(strings($field['value'])->replace('@type(bool)', '')->trim())->toBoolean();
        } elseif (strings($field['value'])->contains('@type(json)')) {
            $field['value'] = strings($field['value'])->replace('@type(json)', '')->trim();
            if (strings($field['value'])->isJson()) {
                $field['value'] = $field['value'];
            } else {
                $field['value'] = collectionFromQueryString($field['value']->toString())->toJson();
            }
        } elseif (strings($field['value'])->contains('@type(array)')) {
            $field['value'] = strings($field['value'])->replace('@type(array)', '')->trim();
            if (strings($field['value'])->isJson()) {
                $field['value'] = serializers()->json()->decode($field['value']->toString());
            } else {
                $field['value'] = collectionFromQueryString($field['value']->toString())->toArray();
            }
        } elseif (strings($field['value'])->contains('@type(collection)')) {
            $field['value'] = strings($field['value'])->replace('@type(collection)', '')->trim();
            if (strings($field['value'])->isJson()) {
                $field['value'] = collection(serializers()->json()->decode($field['value']->toString()));
            } else {
                $field['value'] = collectionFromQueryString($field['value']->toString());
            }
        } elseif (strings($field['value'])->contains('@type(null)')) {
            $field['value'] = null;
        }
    }

    entries()->registry()->set('methods.fetch.field.key', $field['key']);
    entries()->registry()->set('methods.fetch.field.value', $field['value']);
});