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

use function array_keys;
use function array_map;
use function count;
use function explode;
use function Flextype\parsers;
use function Flextype\registry;
use function Flextype\serializers;
use function Glowy\Strings\strings;
use function is_iterable;
use function is_string;
use function parse_str;

// Shortcode: strings
// Usage: (strings) strings to modify (/strings)
parsers()->shortcodes()->addHandler('strings', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.strings.enabled')) {
        return '';
    }

    $content       = $s->getContent() !== null ? parsers()->shortcodes()->parse($s->getContent()) : '';
    $varsDelimeter = $s->getParameter('varsDelimeter') !== null ? parsers()->shortcodes()->parse($s->getParameter('varsDelimeter')) : ',';

    foreach ($s->getParameters() as $key => $value) {
        $vars = $value !== null ? strings($value)->contains($varsDelimeter) ? explode($varsDelimeter, $value) : [$value] : [];

        // Parse shortcodes for each var.
        $vars = array_map(static fn ($v) => parsers()->shortcodes()->parse((string) $v), $vars);

        if ($key === 'append') {
            $content = strings($content)->{'append'}(...$vars)->toString();
        }

        if ($key === 'prepend') {
            $content = strings($content)->{'prepend'}(...$vars)->toString();
        }

        if ($key === 'after') {
            $content = strings($content)->{'after'}($vars[0])->toString();
        }

        if ($key === 'afterLast') {
            $content = strings($content)->{'afterLast'}($vars[0])->toString();
        }

        if ($key === 'before') {
            $content = strings($content)->{'before'}($vars[0])->toString();
        }

        if ($key === 'beforeLast') {
            $content = strings($content)->{'beforeLast'}($vars[0])->toString();
        }

        if ($key === 'lower') {
            $content = strings($content)->{'lower'}()->toString();
        }

        if ($key === 'upper') {
            $content = strings($content)->{'upper'}()->toString();
        }

        if ($key === 'sort') {
            $content = strings($content)->{'wordsSort' . strings($vars[0])->ucfirst()}()->toString();
        }

        if ($key === 'wordsLimit') {
            $content = strings($content)->{'wordsLimit'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 100, isset($vars[1]) ? (string) $vars[1] : '...')->toString();
        }

        if ($key === 'at') {
            $content = strings($content)->{'at'}(strings($vars[0])->toInteger())->toString();
        }

        if ($key === 'base64Decode') {
            $content = strings($content)->{'base64Decode'}()->toString();
        }

        if ($key === 'base64Encode') {
            $content = strings($content)->{'base64Encode'}()->toString();
        }

        if ($key === 'between') {
            $content = strings($content)->{'between'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (string) $vars[1] : '')->toString();
        }

        if ($key === 'camel') {
            $content = strings($content)->{'camel'}()->toString();
        }

        if ($key === 'capitalize') {
            $content = strings($content)->{'capitalize'}()->toString();
        }

        if ($key === 'chars') {
            $content = serializers()->json()->encode(strings($content)->{'chars'}());
        }

        if ($key === 'charsFrequency') {
            $content = serializers()->json()->encode(strings($content)->{'charsFrequency'}());
        }

        if ($key === 'contains') {
            if (isset($vars[0])) {
                parse_str($vars[0], $values);
            } else {
                $values = [];
            }

            $content = strings($content)->{'contains'}(array_keys($values), (isset($vars[1]) ? strings($vars[1])->toBoolean() : true)) ? 'true' : 'false';
        }

        if ($key === 'containsAll') {
            if (isset($vars[0])) {
                parse_str($vars[0], $values);
            } else {
                $values = [];
            }

            $content = strings($content)->{'containsAll'}(array_keys($values), (isset($vars[1]) ? strings($vars[1])->toBoolean() : true)) ? 'true' : 'false';
        }

        if ($key === 'containsAny') {
            if (isset($vars[0])) {
                parse_str($vars[0], $values);
            } else {
                $values = [];
            }

            $content = strings($content)->{'containsAny'}(array_keys($values), isset($vars[1]) ? strings($vars[1])->toBoolean() : true) ? 'true' : 'false';
        }

        if ($key === 'count') {
            $content = (string) strings($content)->{'count'}();
        }

        if ($key === 'crc32') {
            $content = (string) strings($content)->{'crc32'}();
        }

        if ($key === 'countSubString') {
            $content = (string) strings($content)->{'countSubString'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? strings($vars[1])->toBoolean() : true);
        }

        if ($key === 'endsWith') {
            $content = strings($content)->{'endsWith'}(isset($vars[0]) ? (string) $vars[0] : '') ? 'true' : 'false';
        }

        if ($key === 'finish') {
            $content = strings($content)->{'finish'}(isset($vars[0]) ? (string) $vars[0] : '')->toString();
        }

        if ($key === 'firstSegment') {
            $content = strings($content)->{'firstSegment'}(isset($vars[0]) ? (string) $vars[0] : ' ')->toString();
        }

        if ($key === 'format') {
            $formatVars = $vars;
            if (count($formatVars) > 0) {
                $content = strings($content)->{'format'}(...$formatVars)->toString();
            }
        }

        if ($key === 'getEncoding') {
            $content = strings($content)->{'getEncoding'}();
        }

        if ($key === 'setEncoding') {
            $content = strings($content)->{'setEncoding'}(isset($vars[0]) ? (string) $vars[0] : '');
        }

        if ($key === 'hash') {
            $content = strings($content)->{'hash'}(isset($vars[0]) ? (string) $vars[0] : 'md5', isset($vars[1]) ? strings($vars[1])->toBoolean() : false)->toString();
        }

        if ($key === 'increment') {
            $content = strings($content)->{'increment'}(isset($vars[1]) ? (string) $vars[1] : '_', isset($vars[0]) ? (int) $vars[0] : 1)->toString();
        }

        if ($key === 'indexOf') {
            $content = strings($content)->{'indexOf'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (int) $vars[1] : 0, isset($vars[2]) ? strings($vars[2])->toBoolean() : true);
        }

        if ($key === 'indexOfLast') {
            $content = strings($content)->{'indexOfLast'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (int) $vars[1] : 0, isset($vars[2]) ? strings($vars[2])->toBoolean() : true);
        }

        if ($key === 'insert') {
            $content = strings($content)->{'insert'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (int) $vars[1] : 0);
        }

        if ($key === 'isAlpha') {
            $content = strings($content)->{'isAlpha'}() ? 'true' : 'false';
        }

        if ($key === 'isAlphanumeric') {
            $content = strings($content)->{'isAlphanumeric'}() ? 'true' : 'false';
        }

        if ($key === 'isAscii') {
            $content = strings($content)->{'isAscii'}() ? 'true' : 'false';
        }

        if ($key === 'isBase64') {
            $content = strings($content)->{'isBase64'}() ? 'true' : 'false';
        }

        if ($key === 'isBlank') {
            $content = strings($content)->{'isBlank'}() ? 'true' : 'false';
        }

        if ($key === 'isBoolean') {
            $content = strings($content)->{'isBoolean'}() ? 'true' : 'false';
        }

        if ($key === 'isDigit') {
            $content = strings($content)->{'isDigit'}() ? 'true' : 'false';
        }

        if ($key === 'isEmail') {
            $content = strings($content)->{'isEmail'}() ? 'true' : 'false';
        }

        if ($key === 'isEmpty') {
            $content = strings($content)->{'isEmpty'}() ? 'true' : 'false';
        }

        if ($key === 'isEqual') {
            $content = strings($content)->{'isEqual'}(isset($vars[0]) ? (string) $vars[0] : '') ? 'true' : 'false';
        }

        if ($key === 'isFalse') {
            $content = strings($content)->{'isFalse'}() ? 'true' : 'false';
        }

        if ($key === 'isTrue') {
            $content = strings($content)->{'isTrue'}() ? 'true' : 'false';
        }

        if ($key === 'isHexadecimal') {
            $content = strings($content)->{'isHexadecimal'}() ? 'true' : 'false';
        }

        if ($key === 'isHTML') {
            $content = strings($content)->{'isHTML'}() ? 'true' : 'false';
        }

        if ($key === 'isIP') {
            $content = strings($content)->{'isIP'}() ? 'true' : 'false';
        }

        if ($key === 'isJson') {
            $content = strings($content)->{'isJson'}() ? 'true' : 'false';
        }

        if ($key === 'isUpper') {
            $content = strings($content)->{'isUpper'}() ? 'true' : 'false';
        }

        if ($key === 'isLower') {
            $content = strings($content)->{'isLower'}() ? 'true' : 'false';
        }

        if ($key === 'isMAC') {
            $content = strings($content)->{'isMAC'}() ? 'true' : 'false';
        }

        if ($key === 'isNumeric') {
            $content = strings($content)->{'isNumeric'}() ? 'true' : 'false';
        }

        if ($key === 'isPrintable') {
            $content = strings($content)->{'isPrintable'}() ? 'true' : 'false';
        }

        if ($key === 'isPunctuation') {
            $content = strings($content)->{'isPunctuation'}() ? 'true' : 'false';
        }

        if ($key === 'isUrl') {
            $content = strings($content)->{'isUrl'}() ? 'true' : 'false';
        }

        if ($key === 'isSimilar') {
            $content = strings($content)->{'isSimilar'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (float) $vars[1] : 80.0) ? 'true' : 'false';
        }

        if ($key === 'isSerialized') {
            $content = strings($content)->{'isSerialized'}() ? 'true' : 'false';
        }

        if ($key === 'kebab') {
            $content = strings($content)->{'kebab'}()->toString();
        }

        if ($key === 'lastSegment') {
            $content = strings($content)->{'lastSegment'}(isset($vars[0]) ? (string) $vars[0] : ' ')->toString();
        }

        if ($key === 'length') {
            $content = strings($content)->{'length'}();
        }

        if ($key === 'limit') {
            $content = strings($content)->{'limit'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 100, isset($vars[1]) ? (string) $vars[1] : '...')->toString();
        }

        if ($key === 'lines') {
            $content = serializers()->json()->encode(strings($content)->{'lines'}());
        }

        if ($key === 'md5') {
            $content = strings($content)->{'md5'}(isset($vars[0]) ? strings($vars[0])->toBoolean() : false)->toString();
        }

        if ($key === 'move') {
            $content = strings($content)->{'move'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 0, isset($vars[1]) ? strings($vars[1])->toInteger() : 0, isset($vars[2]) ? strings($vars[2])->toInteger() : 0)->toString();
        }

        if ($key === 'normalizeNewLines') {
            $content = strings($content)->{'normalizeNewLines'}()->toString();
        }

        if ($key === 'normalizeSpaces') {
            $content = strings($content)->{'normalizeSpaces'}()->toString();
        }

        if ($key === 'offsetExists') {
            $content = strings($content)->{'offsetExists'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 0) ? 'true' : 'false';
        }

        if ($key === 'offsetGet') {
            $content = strings($content)->{'offsetGet'}(isset($vars[0]) ? strings($vars[0])->toString() : 0);
        }

        if ($key === 'padBoth') {
            $content = strings($content)->{'padBoth'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 0, isset($vars[1]) ? (string) $vars[1] : ' ')->toString();
        }

        if ($key === 'padLeft') {
            $content = strings($content)->{'padLeft'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 0, isset($vars[1]) ? (string) $vars[1] : ' ')->toString();
        }

        if ($key === 'padRight') {
            $content = strings($content)->{'padRight'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 0, isset($vars[1]) ? (string) $vars[1] : ' ')->toString();
        }

        if ($key === 'quotesToEntities') {
            $content = strings($content)->{'quotesToEntities'}()->toString();
        }

        if ($key === 'random') {
            $content = strings($content)->{'random'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 64, isset($vars[1]) ? (string) $vars[1] : '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')->toString();
        }

        if ($key === 'reduceSlashes') {
            $content = strings($content)->{'reduceSlashes'}()->toString();
        }

        if ($key === 'repeat') {
            $content = strings($content)->{'repeat'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 1)->toString();
        }

        if ($key === 'replace') {
            $content = strings($content)->{'replace'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (string) $vars[1] : '')->toString();
        }

        if ($key === 'replaceDashes') {
            $content = strings($content)->{'replaceDashes'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? strings($vars[1])->toBoolean() : false)->toString();
        }

        if ($key === 'replaceFirst') {
            $content = strings($content)->{'replaceFirst'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (string) $vars[1] : '')->toString();
        }

        if ($key === 'replaceLast') {
            $content = strings($content)->{'replaceLast'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? (string) $vars[1] : '')->toString();
        }

        if ($key === 'replaceNonAlpha') {
            $content = strings($content)->{'replaceNonAlpha'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? strings($vars[1])->toBoolean() : false)->toString();
        }

        if ($key === 'replaceNonAlphanumeric') {
            $content = strings($content)->{'replaceNonAlphanumeric'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? strings($vars[1])->toBoolean() : false)->toString();
        }

        if ($key === 'replacePunctuations') {
            $content = strings($content)->{'replacePunctuations'}(isset($vars[0]) ? (string) $vars[0] : '', isset($vars[1]) ? strings($vars[1])->toBoolean() : false)->toString();
        }

        if ($key === 'reverse') {
            $content = strings($content)->{'reverse'}()->toString();
        }

        if ($key === 'segment') {
            $content = strings($content)->{'segment'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 1, isset($vars[1]) ? (string) $vars[1] : ' ')->toString();
        }

        if ($key === 'segments') {
            $content = serializers()->json()->encode(strings($content)->{'segments'}(isset($vars[0]) ? (string) $vars[0] : ' '));
        }

        if ($key === 'sha1') {
            $content = strings($content)->{'sha1'}(isset($vars[0]) ? strings($vars[0])->toBoolean() : false)->toString();
        }

        if ($key === 'sha256') {
            $content = strings($content)->{'sha256'}(isset($vars[0]) ? strings($vars[0])->toBoolean() : false)->toString();
        }

        if ($key === 'shuffle') {
            $content = strings($content)->{'shuffle'}()->toString();
        }

        if ($key === 'similarity') {
            $content = (string) strings($content)->{'similarity'}(isset($vars[0]) ? (string) $vars[0] : '');
        }

        if ($key === 'snake') {
            $content = strings($content)->{'snake'}(isset($vars[0]) ? (string) $vars[0] : '_')->toString();
        }

        if ($key === 'start') {
            $content = strings($content)->{'start'}(isset($vars[0]) ? (string) $vars[0] : '')->toString();
        }

        if ($key === 'startsWith') {
            $content = strings($content)->{'startsWith'}(isset($vars[0]) ? (string) $vars[0] : '') ? 'true' : 'false';
        }

        if ($key === 'stripQuotes') {
            $content = strings($content)->{'stripQuotes'}()->toString();
        }

        if ($key === 'stripSpaces') {
            $content = strings($content)->{'stripSpaces'}()->toString();
        }

        if ($key === 'studly') {
            $content = strings($content)->{'studly'}()->toString();
        }

        if ($key === 'substr') {
            $content = strings($content)->{'substr'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 0, isset($vars[1]) ? (string) $vars[1] : null)->toString();
        }

        if ($key === 'trim') {
            $content = strings($content)->{'trim'}()->toString();
        }

        if ($key === 'trimLeft') {
            $content = strings($content)->{'trimLeft'}()->toString();
        }

        if ($key === 'trimRight') {
            $content = strings($content)->{'trimRight'}()->toString();
        }

        if ($key === 'trimSlashes') {
            $content = strings($content)->{'trimSlashes'}()->toString();
        }

        if ($key === 'ucfirst') {
            $content = strings($content)->{'ucfirst'}()->toString();
        }

        if ($key === 'wordsCount') {
            $content = (string) strings($content)->{'wordsCount'}(isset($vars[0]) ? (string) $vars[0] : '?!;:,.');
        }

        if ($key === 'words') {
            $content = serializers()->json()->encode(strings($content)->{'words'}(isset($vars[0]) ? (string) $vars[0] : '?!;:,.'));
        }

        if ($key !== 'wordsFrequency') {
            continue;
        }

        $content = serializers()->json()->encode(strings($content)->{'wordsFrequency'}(isset($vars[0]) ? strings($vars[0])->toInteger() : 2, isset($vars[1]) ? (string) $vars[1] : '.', isset($vars[2]) ? (string) $vars[2] : ','));
    }

    return (string) $content;
});
