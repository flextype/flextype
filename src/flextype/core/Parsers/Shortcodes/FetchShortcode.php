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

use GuzzleHttp\Psr7\Response;
use function array_keys;
use function Flextype\fetch;
use function Flextype\collection;
use function Flextype\collectionFromJson;
use function Flextype\entries;
use function Flextype\parsers;
use function Flextype\registry;
use function Glowy\Strings\strings;
use function Glowy\Filesystem\filesystem;
use function parse_str;

// Shortcode: fetch
// Usage: (fetch resource:'blog/post-1' options:'' field:'title')
//        (fetch:'blog/post-1')
parsers()->shortcodes()->addHandler('fetch', static function (ShortcodeInterface $s) {
    $params = $s->getParameters();

    $fetchHelper = function ($params) use ($s) {
        
        // Get the resource parameter.
        $resource = parsers()->shortcodes()->parse($params['resource']);
      
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
        $original = entries()->registry()->get('methods.fetch');

        // Do fetch the data from the resource.
        $result = fetch($resource, $options);

        // Restore original entry data
        entries()->registry()->set('methods.fetch', $original);
                
        // Return empty result if result is instance of Response (for e.g. if async = true).
        if ($result instanceof Response) {
            return '';
        }

        if ($result->count() > 0) {
   
            $result = $result->toJson();

            // Get specific field value
            if ($s->getParameter('field') !== null) {
                $result = collectionFromJson($result)->get(parsers()->shortcodes()->parse($s->getParameter('field')), $s->getParameter('default') !== null ? parsers()->shortcodes()->parse($s->getParameter('default')) : '');
            }
            
            return $result;
        }

        return '';
    };
    
    if (isset($params['resource'])) {
        return $fetchHelper($params);
    }

    if ($s->getBBCode() !== null) {
        $params = ['resource' => $s->getBBCode(), 'options' => isset($params['options']) ? $params['options'] : ''];
        return $fetchHelper($params);
    }

    return '';
});