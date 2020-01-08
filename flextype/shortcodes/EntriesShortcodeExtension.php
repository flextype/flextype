<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [entries_fetch id="entry-id" field="field-name" default="default-value"]
$flextype['shortcodes']->addHandler('entries_fetch', static function (ShortcodeInterface $s) use ($flextype) {
    return Arr::get($flextype['entries']->fetch($s->getParameter('id')), $s->getParameter('field'), $s->getParameter('default'));
});
