<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [registry_get name="item-name" default="default-value"]
$flextype['shortcodes']->addHandler('registry_get', static function (ShortcodeInterface $s) use ($flextype) {
    return $flextype['registry']->get($s->getParameter('name'), $s->getParameter('default'));
});
