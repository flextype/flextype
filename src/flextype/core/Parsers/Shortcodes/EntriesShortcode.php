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
use Flextype\Entries\Entries;

use function arrays;
use function entries;
use function parsers;
use function registry;

// Shortcode: [entries]
parsers()->shortcodes()->addHandler('entries', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.entries.enabled')) {
        return '';
    }

    $varsDelimeter = $s->getParameter('varsDelimeter') ?: '|';

    if ($s->getParameter('fetch') != null) {

        // Get vars
        foreach($s->getParameters() as $key => $value) {
            $vars = $value !== null ? strings($value)->contains($varsDelimeter) ? explode($varsDelimeter, $value) : [$value] : [];
        }

        // Set options
        if (isset($vars[1])) {
            parse_str($vars[1], $options);
        } else {
            $options = [];
        }

        // Prepare options
        $options = collection($options)->dot()->map(function($value) {
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
        })->undot()->toArray();
        
        // Backup current entry data
        $original = entries()->registry()['methods.fetch'];
        
        // Fetch entry
        $result = entries()->fetch($vars[0], $options);

        // Restore original entry data
        entries()->registry()->set('methods.fetch', $original);

        // Return entry as a json string
        return $result->toJson();
    }

    return '';
});