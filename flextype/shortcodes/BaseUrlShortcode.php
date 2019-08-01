<?php

declare(strict_types=1);

/**
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Slim\Http\Environment;
use Slim\Http\Uri;

// Shortcode: [base_url]
$flextype['shortcodes']->addHandler('base_url', static function () {
    return Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
});
