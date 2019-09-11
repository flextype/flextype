<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [snippets_exec id="snippet-name"]
$flextype['shortcodes']->addHandler('snippets_exec', static function (ShortcodeInterface $s) use ($flextype) {
    return $flextype['snippets']->exec($s->getParameter('id'));
});
