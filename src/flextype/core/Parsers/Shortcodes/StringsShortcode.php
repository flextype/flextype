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

    foreach($s->getParameters() as $key => $value) {

        $vars  = $value !== null ? strings($value)->contains($varsDelimeter) ? explode($varsDelimeter, $value) : $value : [];

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
            $content = strings($content)->{'after'}($vars)->toString();
        }

        if ($key == 'afterLast') {
            $content = strings($content)->{'afterLast'}($vars)->toString();
        }

        if ($key == 'before') {
            $content = strings($content)->{'before'}($vars)->toString();
        }
        
        if ($key == 'beforeLast') {
            $content = strings($content)->{'beforeLast'}($vars)->toString();
        }

        if ($key == 'lower') {
            $content = strings($content)->{'lower'}()->toString();
        }

        if ($key == 'upper') {
            $content = strings($content)->{'upper'}()->toString();
        }

        if ($key == 'sort') {
            $content = strings($content)->{'wordsSort' . strings($vars)->ucfirst()}()->toString();
        }

        if ($key == 'wordsLimit') {
            $content = strings($content)->{'wordsLimit'}(isset($vars[0]) ? (int) $vars[0] : 100, isset($vars[1]) ? (string) $vars[1] : '...')->toString();
        }

        if ($key == 'at') {
            $content = strings($content)->{'at'}((int) $vars)->toString();
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
            $content = strings($content)->{'contains'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (bool) $vars[1] : true) ? "true" : "false"; 
        }
    }
    
    return (string) $content;
});