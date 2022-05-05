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

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use function parsers;
use function registry;

// Shortcode: [strings] strings to modify [/strings]
parsers()->shortcodes()->addHandler('strings', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.strings.enabled')) {
        return '';
    }
    
    $content       = $s->getContent();
    $varsDelimeter = $s->getParameter('delimeter') ?: '|';

    foreach($s->getParameters() as $key => $value) {

        $values = $value !== null ? strings($value)->contains($varsDelimeter) ? explode($varsDelimeter, $value) : $value : [];
        
        if ($key == 'append') {
            if (is_iterable($values)) {
                $content = strings($content)->{'append'}(...$values)->toString();
            } else {
                $content = strings($content)->{'append'}($values)->toString();
            }
        }

        if ($key == 'prepend') {
            if (is_iterable($values)) {
                $content = strings($content)->{'prepend'}(...$values)->toString();
            } else {
                $content = strings($content)->{'prepend'}($values)->toString();
            }
        }

        if ($key == 'after') {
            $content = strings($content)->{'after'}($values)->toString();
        }

        if ($key == 'afterLast') {
            $content = strings($content)->{'afterLast'}($values)->toString();
        }

        if ($key == 'before') {
            $content = strings($content)->{'before'}($values)->toString();
        }
        
        if ($key == 'beforeLast') {
            $content = strings($content)->{'beforeLast'}($values)->toString();
        }

        if ($key == 'lower') {
            $content = strings($content)->{'lower'}()->toString();
        }

        if ($key == 'upper') {
            $content = strings($content)->{'upper'}()->toString();
        }

        if ($key == 'sort') {
            $content = strings($content)->{'wordsSort' . strings($values)->ucfirst()}()->toString();
        }

        if ($key == 'wordsLimit') {
            $content = strings($content)->{'wordsLimit'}(isset($values[0]) ? (int) $values[0] : 100, isset($values[1]) ? (string) $values[1] : '...')->toString();
        }

        if ($key == 'at') {
            $content = strings($content)->{'at'}((int) $values)->toString();
        }
        
        if ($key == 'base64Decode') {
            $content = strings($content)->{'base64Decode'}()->toString();
        }

        if ($key == 'base64Encode') {
            $content = strings($content)->{'base64Encode'}()->toString();
        }

        if ($key == 'between') {
            $content = strings($content)->{'between'}(isset($values[0]) ? (string) $values[0] : '', isset($values[1]) ? (string) $values[1] : '')->toString();
        }

        if ($key == 'camel') {
            $content = strings($content)->{'camel'}()->toString();
        }

        if ($key == 'capitalize') {
            $content = strings($content)->{'capitalize'}()->toString();
        }

        if ($key == 'chars') {
            $content = serializers()->json()->encode(strings($content)->{'chars'}());
        }

        if ($key == 'charsFrequency') {
            $content = serializers()->json()->encode(strings($content)->{'charsFrequency'}());
        }
    }
    
    return (string) $content;
});