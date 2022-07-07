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

use function Flextype\entries;
use function Flextype\parsers;
use function Flextype\registry;

// Shortcode: var
// Usage: (var:foo)
//        (var get:foo)
//        (var set:foo value:Foo)
//        (var set:foo) Foo (/var)
parsers()->shortcodes()->addHandler('var', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.var.enabled')) {
        return '';
    }
    
    $params = $s->getParameters();

    if (isset($params['set'])) {
        if (isset($params['value'])) {
            $value = $params['value'];
        } else {
            $value = $s->getContent() ?? '';
        }

        entries()->registry()->set('methods.fetch.result.vars.' . parsers()->shortcodes()->parse($params['set']), parsers()->shortcodes()->parse($value));

        return '';
    }

    if (isset($params['get'])) {
        return entries()->registry()->get('methods.fetch.result.vars.' . parsers()->shortcodes()->parse($params['get']));
    }

    if ($s->getBBCode() !== null) {
        return entries()->registry()->get('methods.fetch.result.vars.' . parsers()->shortcodes()->parse($s->getBBCode()));
    }

    return '';
});
