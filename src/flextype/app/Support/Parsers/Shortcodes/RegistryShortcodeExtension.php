<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [registry_get name="item-name" default="default-value"]
$flextype['shortcode']->add('registry_get', function (ShortcodeInterface $s) use ($flextype) {
    return $flextype['registry']->get($s->getParameter('name'), $s->getParameter('default'));
});
