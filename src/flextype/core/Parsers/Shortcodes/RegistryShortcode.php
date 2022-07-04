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
use function Glowy\Registry\registry;

// Shortcode: registry
// Usage: (registry get id:'flextype.manifest.version')
parsers()->shortcodes()->addHandler('registry', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.registry.enabled')) {
        return '';
    }
    
    $result = '';
    $params = $s->getParameters();

    if (collection(array_keys($params))->filter(fn ($v) => $v == 'get')->count() > 0 && 
        isset($params['id']) && 
        registry()->get('flextype.settings.parsers.shortcodes.shortcodes.registry.get.enabled') === true) {

        $id      = parsers()->shortcodes()->parse($params['id']);
        $default = (isset($params['default'])) ? parsers()->shortcodes()->parse($params['default']) : null;
        $result  = registry()->get($id, $default);

        return is_array($result) ? serializers()->json()->encode($result) : $result;
    }

    return '';
});
