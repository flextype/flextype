<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [icon value="fab fa-apple"]
$flextype['shortcodes']->addHandler('icon', static function (ShortcodeInterface $s) {
    return IconController::icon($s->getParameter('value'));
});
