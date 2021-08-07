<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [media_files_fetch id="media-id" field="field-name" default="default-value"]
parsers()->shortcodes()->addHandler('media_files_fetch', static function (ShortcodeInterface $s) {
    
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.media.enabled')) {
        return '';
    }

    return arrays(flextype('media')->files()->fetch($s->getParameter('id')))->get($s->getParameter('field'), $s->getParameter('default'));
});

