<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

use function arrays;
use function entries;
use function parsers;
use function registry;

// Shortcode: [entries_fetch id="entry-id" field="field-name" default="default-value"]
parsers()->shortcodes()->addHandler('entries_fetch', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.entries.enabled')) {
        return '';
    }

    return arrays(entries()->fetch($s->getParameter('id')))->get($s->getParameter('field'), $s->getParameter('default'));
});
