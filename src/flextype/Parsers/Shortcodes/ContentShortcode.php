<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [content_fetch id="content-id" field="field-name" default="default-value"]
if (registry()->get('flextype.settings.parsers.shortcodes.shortcodes.content.enabled')) {
    parsers()->shortcodes()->addHandler('content_fetch', static function (ShortcodeInterface $s) {
        return arrays(content()->fetch($s->getParameter('id')))->get($s->getParameter('field'), $s->getParameter('default'));
    });
}
