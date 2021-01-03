<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [entries_fetch id="entry-id" field="field-name" default="default-value"]
if (flextype('registry')->get('flextype.settings.parsers.shortcode.shortcodes.entries.enabled')) {
    flextype('parsers')->shortcode()->addHandler('entries_fetch', static function (ShortcodeInterface $s) {
        return arrays(flextype('entries')->fetch($s->getParameter('id')))->get($s->getParameter('field'), $s->getParameter('default'));
    });
}
