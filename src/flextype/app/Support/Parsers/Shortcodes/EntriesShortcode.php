<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Flextype\Component\Arrays\Arrays;

if ($container->registry->get('flextype.settings.shortcode.shortcodes.entries.enabled')) {

    // Shortcode: [entries_fetch id="entry-id" field="field-name" default="default-value"]
    $container['shortcode']->addHandler('entries_fetch', function (ShortcodeInterface $s) use ($container) {
        return Arrays::get($container['entries']->fetch($s->getParameter('id')), $s->getParameter('field'), $s->getParameter('default'));
    });
}
