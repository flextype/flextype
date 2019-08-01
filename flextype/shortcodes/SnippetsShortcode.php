<?php

declare(strict_types=1);

/**
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [snippets_exec id="snippet-name"]
$flextype['shortcodes']->addHandler('snippets_exec', static function (ShortcodeInterface $s) use ($flextype) {
    return $flextype['snippets']->exec($s->getParameter('id'));
});
