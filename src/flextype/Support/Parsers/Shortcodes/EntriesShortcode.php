<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [content_fetch id="entry-id" field="field-name" default="default-value"]
if (flextype('registry')->get('flextype.settings.parsers.shortcode.shortcodes.content.enabled')) {
    flextype('parsers')->shortcode()->addHandler('content_fetch', static function (ShortcodeInterface $s) {
        return arrays(flextype('content')->fetch($s->getParameter('id')))->get($s->getParameter('field'), $s->getParameter('default'));
    });
}
