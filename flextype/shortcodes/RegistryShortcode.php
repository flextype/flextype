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

// Shortcode: [registry_get name="item-name" default="default-value"]
$flextype['shortcodes']->addHandler('registry_get', function (ShortcodeInterface $s) use ($flextype) {
    return $flextype['registry']->get($s->getParameter('name'), $s->getParameter('default'));
});
