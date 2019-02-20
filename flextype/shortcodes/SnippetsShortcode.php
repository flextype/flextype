<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Snippets
// Shortcode: [snippets fetch=snippet-name]
Shortcodes::shortcode()->addHandler('snippets', function(ShortcodeInterface $s) {
    return Snippets::get($s->getParameter('fetch'));
});
