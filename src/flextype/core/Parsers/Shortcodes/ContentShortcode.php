<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

use function arrays;
use function content;
use function parsers;
use function registry;

// Shortcode: [content_fetch id="content-id" field="field-name" default="default-value"]
parsers()->shortcodes()->addHandler('content_fetch', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.content.enabled')) {
        return '';
    }

    return arrays(content()->fetch($s->getParameter('id')))->get($s->getParameter('field'), $s->getParameter('default'));
});
