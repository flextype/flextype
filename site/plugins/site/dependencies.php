<?php

declare(strict_types=1);

/**
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

/**
 * Add site controller to Flextype container
 */
$flextype['SiteController'] = static function ($container) {
    return new SiteController($container);
};
