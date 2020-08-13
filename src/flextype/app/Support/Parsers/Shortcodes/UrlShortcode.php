<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Slim\Http\Environment;
use Slim\Http\Uri;

if ($container->registry->get('flextype.settings.shortcode.shortcodes.url.enabled')) {

    // Shortcode: [url]
    $container['shortcode']->addHandler('url', function () use ($container) {
        if ($container['registry']->has('flextype.settings.url') && $container['registry']->get('flextype.settings.url') !== '') {
            return $container['registry']->get('flextype.settings.url');
        }

        return Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
    });
}
