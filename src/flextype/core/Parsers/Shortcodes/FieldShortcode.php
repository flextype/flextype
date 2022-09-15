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

// Shortcode: field
// Usage: (field:title)
//        (field get:foo default:Foo)
//        (field get:foo) Default (/field)
//        (field set:foo value:Foo)
//        (field set:foo) Foo (/field)
//        (field unset:foo)
//        (field delete:foo)
parsers()->shortcodes()->addHandler('field', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.field.enabled')) {
        return '';
    }
    
    $params = $s->getParameters();

    // set
    if (isset($params['set'])) {
        if (isset($params['value'])) {
            $value = $params['value'];
        } else {
            $value = $s->getContent() ?? '';
        }

        entries()->registry()->set('methods.fetch.result.' . parsers()->shortcodes()->parse($params['set']), parsers()->shortcodes()->parse($value));

        return '';
    }

    // get
    if (isset($params['get'])) {
        $default = isset($params['default']) ? $params['default'] : $s->getContent() ?? '';
        return entries()->registry()->get('methods.fetch.result.' . parsers()->shortcodes()->parse($params['get']), $default);
    }

    if ($s->getBBCode() !== null) {
        return entries()->registry()->get('methods.fetch.result.' . parsers()->shortcodes()->parse($s->getBBCode()));
    }

    // unset
    if (isset($params['unset'])) {
        entries()->registry()->set('methods.fetch.result.' . parsers()->shortcodes()->parse($params['unset']), null);
        return '';
    }

    // delete
    if (isset($params['delete'])) {
        entries()->registry()->delete('methods.fetch.result.' . parsers()->shortcodes()->parse($params['delete']));
        return '';
    }
    
    return '';
});
