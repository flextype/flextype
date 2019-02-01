<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Snippets
// Shortcode: [snippets get=snippet-name]
Shortcodes::shortcode()->addHandler('snippets', function(ShortcodeInterface $s) {
    return Snippets::get($s->getParameter('get'));
});
