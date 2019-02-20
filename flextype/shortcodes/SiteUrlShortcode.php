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

use Flextype\Component\Http\Http;
use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Shortcode: [site_url]
Shortcodes::shortcode()->addHandler('site_url', function() {
    return Http::getBaseUrl();
});
