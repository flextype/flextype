<?php

declare(strict_types=1);

/**
 * @link http://digital.flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

/**
 * Add Form Model to Flextype container
 */
$flextype['form'] = static function ($container) {
    return new Form($container);
};

/**
 * Add Fieldsets Model to Flextype container
 */
$flextype['fieldsets'] = static function ($container) {
    return new Fieldsets($container);
};

/**
 * Add Form Twig extension
 */
$flextype->view->addExtension(new FormTwigExtension($flextype));
