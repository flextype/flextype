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
    
    $content        = $s->getContent();
    $varsDelimeter  = $s->getParameter('varsDelimeter') ?: '|';
    $itemsDelimeter = $s->getParameter('itemsDelimeter') ?: ',';

    foreach($s->getParameters() as $key => $value) {

        $vars = $value !== null ? strings($value)->contains($varsDelimeter) ? explode($varsDelimeter, $value) : [$value] : [];

        if ($key == 'append') {
            if (is_iterable($vars)) {
                $content = strings($content)->{'append'}(...$vars)->toString();
            } else {
                $content = strings($content)->{'append'}($vars)->toString();
            }
        }

        if ($key == 'prepend') {
            if (is_iterable($vars)) {
                $content = strings($content)->{'prepend'}(...$vars)->toString();
            } else {
                $content = strings($content)->{'prepend'}($vars)->toString();
            }
        }

        if ($key == 'after') {
            $content = strings($content)->{'after'}($vars[0])->toString();
        }

        if ($key == 'afterLast') {
            $content = strings($content)->{'afterLast'}($vars[0])->toString();
        }

        if ($key == 'before') {
            $content = strings($content)->{'before'}($vars[0])->toString();
        }
        
        if ($key == 'beforeLast') {
            $content = strings($content)->{'beforeLast'}($vars[0])->toString();
        }

        if ($key == 'lower') {
            $content = strings($content)->{'lower'}()->toString();
        }

        if ($key == 'upper') {
            $content = strings($content)->{'upper'}()->toString();
        }

        if ($key == 'sort') {
            $content = strings($content)->{'wordsSort' . strings($vars[0])->ucfirst()}()->toString();
        }

        if ($key == 'wordsLimit') {
            $content = strings($content)->{'wordsLimit'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 100, isset($vars[1]) ? (string) $vars[1] : '...')->toString();
        }

        if ($key == 'at') {
            $content = strings($content)->{'at'}(strings($vars[0])->toInteger())->toString();
        }
        
        if ($key == 'base64Decode') {
            $content = strings($content)->{'base64Decode'}()->toString();
        }

        if ($key == 'base64Encode') {
            $content = strings($content)->{'base64Encode'}()->toString();
        }

        if ($key == 'between') {
            $content = strings($content)->{'between'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (string) $vars[1] : '')->toString();
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

        if ($key == 'contains') {
            $content = strings($content)->{'contains'}(isset($vars[0]) ? explode($itemsDelimeter, $vars[0]) : '', isset($vars[1]) ? strings($vars[1])->toBoolean() : true) ? "true" : "false"; 
        }

        if ($key == 'containsAll') {
            $content = strings($content)->{'containsAll'}(isset($vars[0]) ? explode($itemsDelimeter, $vars[0]) : '', isset($vars[1]) ? strings($vars[1])->toBoolean() : true) ? "true" : "false"; 
        }

        if ($key == 'containsAny') {
            $content = strings($content)->{'containsAny'}(isset($vars[0]) ? explode($itemsDelimeter, $vars[0]) : '', isset($vars[1]) ? strings($vars[1])->toBoolean() : true) ? "true" : "false"; 
        }

        if ($key == 'count') {
            $content = (string) strings($content)->{'count'}();
        }

        if ($key == 'crc32') {
            $content = (string) strings($content)->{'crc32'}();
        }

        if ($key == 'countSubString') {
            $content = (string) strings($content)->{'countSubString'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? strings($vars[1])->toBoolean() : true);
        }

        if ($key == 'endsWith') {
            $content = strings($content)->{'endsWith'}(isset($vars[0]) ? (string) $vars[0] : '')  ? "true" : "false";
        }

        if ($key == 'finish') {
            $content = strings($content)->{'finish'}(isset($vars[0]) ? (string) $vars[0] : '')->toString();
        }

        if ($key == 'firstSegment') {
            $content = strings($content)->{'firstSegment'}(isset($vars[0]) ? (string) $vars[0] : ' ')->toString();
        }
    }
    
    return (string) $content;
});