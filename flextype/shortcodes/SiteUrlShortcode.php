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

// Shortcode: [site_url]
$flextype['shortcodes']->addHandler('site_url', function() use ($flextype) {
    return \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER))->getBaseUrl();
});
