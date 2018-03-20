<?php namespace Flextype;

use Url;

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Add {site_url} shortcode
Shortcodes::add('site_url', function () {
    return Url::getBase();
});
