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

// Shortcode: [if]
parsers()->shortcodes()->addHandler('if', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.if.enabled')) {
        return '';
    }

    $prepareValue = function($value) {
        if(strings($value)->isInteger()) {
            $value = strings($value)->toInteger();
        } elseif(strings($value)->isFloat()) {
            $value = strings($value)->toFloat();
        } elseif(strings($value)->isBoolean()) {
            $value = strings($value)->toBoolean();
        } elseif(strings($value)->isNull()) {
            $value = strings($value)->toNull();
        } else {
            $value = (string) $value;
        }
        return $value;
    };

    $result   = false;
    $operator = $s->getParameter('operator');
    $val1     = $prepareValue($s->getParameter('val1'));
    $val2     = $prepareValue($s->getParameter('val2'));
    $encoding = $s->getParameter('encoding') ? $s->getParameter('encoding') : 'utf-8';

    switch ($operator) {
        case 'lt':
        case '<':
            $result = (bool) ($val1 < $val2);
            break;

        case 'gt':
        case '>':
            $result = (bool) ($val1 > $val2);
            break;

        case 'lte':
        case '<=':
            $result = (bool) ($val1 <= $val2);
            break;

        case 'gte':
        case '>=':
            $result = (bool) ($val1 >= $val2);
            break;

        case 'eq':
        case '=':
            $result = (bool) ($val1 === $val2);
            break;

        case 'neq':
        case '<>':
        case '!=':
            $result = (bool) ($val1 !== $val2);
            break;
            
        case 'contains':
        case 'like':
            $result = (bool) (mb_strpos((string) $val1, (string) $val2, 0, $encoding) !== false);
            break;

        case 'ncontains':
        case 'nlike':
            $result = (bool) (mb_strpos((string) $val1, (string) $val2, 0, $encoding) === false);
            break;

        case 'starts_with':
            $result = (bool) (strncmp((string) $val1, (string) $val2, mb_strlen((string) $val2)) === 0);
            break;

        case 'ends_with':
            $result = (bool) (mb_substr((string) $val1, -mb_strlen((string) $val2), null, $encoding) === $val2);
            break;
            
        case 'newer':
            $result = (bool) (strtotime((string) $val1) > strtotime((string) $val2));
            break;

        case 'older':
            $result = (bool) (strtotime((string) $val1) < strtotime((string) $val2));
            break;

        case 'regexp':
            $val2 = (string) $val2;
            $result = (bool) (preg_match("/{$val2}/ium", (string) $val1));
            break;

        case 'nregexp':
            $val2 = (string) $val2;
            $result = (bool) (! preg_match("/{$val2}/ium", (string) $val1));
            break;

        default:
            $result = false;
            break;
    }

    return $result ? $s->getContent() : '';
});