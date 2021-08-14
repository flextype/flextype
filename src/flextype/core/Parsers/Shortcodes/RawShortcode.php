<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\EventHandler\FilterRawEventHandler;
use Thunder\Shortcode\Events;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

use function parsers;

// Shortcode: [raw]
parsers()->shortcodes()->addHandler('raw', static function (ShortcodeInterface $s) {
    return $s->getContent();
});

parsers()->shortcodes()->addEventHandler(Events::FILTER_SHORTCODES, new FilterRawEventHandler(['raw']));
