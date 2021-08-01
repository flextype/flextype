<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [entries_fetch id="entry-id" field="field-name" default="default-value"]
if (registry()->get('flextype.settings.parsers.shortcodes.entries.enabled')) {
    parsers()->shortcodes()->addHandler('entries_fetch', static function (ShortcodeInterface $s) {
        return arrays(entries()->fetch($s->getParameter('id')))->get($s->getParameter('field'), $s->getParameter('default'));
    });
}
