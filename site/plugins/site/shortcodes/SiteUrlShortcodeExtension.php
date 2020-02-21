<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

// Shortcode: [site_url]
$flextype['shortcodes']->addHandler('site_url', static function () use ($flextype) {
    return $flextype->SiteController->getSiteUrl();
});
