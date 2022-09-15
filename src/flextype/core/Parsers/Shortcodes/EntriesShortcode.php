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
use function Flextype\collection;
use function Flextype\collectionFromJson;
use function Flextype\entries;
use function Flextype\parsers;
use function Flextype\registry;
use function Glowy\Strings\strings;
use function parse_str;

// Shortcode: entries
// Usage: (entries fetch:'blog/post-1' field:'title')
parsers()->shortcodes()->addHandler('entries', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.entries.enabled')) {
        return '';
    }

    $result = '';
    $params = $s->getParameters();

    if (
        collection(array_keys($params))->filter(static fn ($v) => $v === 'fetch')->count() > 0 &&
        isset($params['id']) &&
        registry()->get('flextype.settings.parsers.shortcodes.shortcodes.entries.fetch.enabled') === true
    ) {
        $id = parsers()->shortcodes()->parse($params['id']);

        // Set options
        if (isset($params['options'])) {
            parse_str(parsers()->shortcodes()->parse($params['options']), $options);
        } else {
            $options = [];
        }

        // Prepare options
        $options = collection($options)->dot()->map(static function ($value) {
            if (strings($value)->isInteger()) {
                $value = strings($value)->toInteger();
            } elseif (strings($value)->isFloat()) {
                $value = strings($value)->toFloat();
            } elseif (strings($value)->isBoolean()) {
                $value = strings($value)->toBoolean();
            } elseif (strings($value)->isNull()) {
                $value = strings($value)->toNull();
            } else {
                $value = (string) $value;
            }

            return $value;
        })->undot()->toArray();

        // Backup current entry data
        $original = entries()->registry()['methods.fetch'];

        // Fetch entry
        $result = entries()->fetch($id, $options);

        // Restore original entry data
        entries()->registry()->set('methods.fetch', $original);

        // Convert entry as a json string
        $result = $result->toJson();

        // Get specific field value
        if ($s->getParameter('field') !== null) {
            $result = collectionFromJson($result)->get(parsers()->shortcodes()->parse($s->getParameter('field')), $s->getParameter('default') !== null ? parsers()->shortcodes()->parse($s->getParameter('default')) : '');
        }
    }

    return $result;
});
