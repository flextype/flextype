<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Thunder\Shortcode\EventHandler\FilterRawEventHandler;
use Thunder\Shortcode\Events;

if ($container->registry->get('flextype.settings.shortcode.shortcodes.raw.enabled')) {

    // Shortcode: [raw]
    $container['shortcode']->addHandler('raw', function (ShortcodeInterface $s) use ($container) {
        return $s->getContent();
    });

    $container['shortcode']->addEventHandler(Events::FILTER_SHORTCODES, new FilterRawEventHandler(['raw']));
}
