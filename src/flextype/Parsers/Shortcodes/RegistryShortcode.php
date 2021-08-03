<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [registry_get name="item-name" default="default-value"]
if (registry()->get('flextype.settings.parsers.shortcodes.registry.enabled')) {
    parsers()->shortcodes()->addHandler('registry_get', static function (ShortcodeInterface $s) {
        return registry()->get($s->getParameter('name'), $s->getParameter('default'));
    });
}
