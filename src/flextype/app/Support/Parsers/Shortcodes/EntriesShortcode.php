<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Arrays\Arrays;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

if (flextype('registry')->get('flextype.settings.shortcode.shortcodes.entries.enabled')) {
    // Shortcode: [entries_fetch id="entry-id" field="field-name" default="default-value"]
    flextype('shortcode')->addHandler('entries_fetch', static function (ShortcodeInterface $s) {
        return Arrays::get(flextype('entries')->fetch($s->getParameter('id')), $s->getParameter('field'), $s->getParameter('default'));
    });
}
