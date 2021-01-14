<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [media_files_fetch id="media-id" field="field-name" default="default-value"]
if (flextype('registry')->get('flextype.settings.parsers.shortcode.shortcodes.media.enabled')) {
    flextype('parsers')->shortcode()->addHandler('media_files_fetch', static function (ShortcodeInterface $s) {
        return arrays(flextype('media')->files()->fetch($s->getParameter('id')))->get($s->getParameter('field'), $s->getParameter('default'));
    });
}
