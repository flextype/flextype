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
 * Add Form Controller to Flextype container
 */
$flextype['FormController'] = static function ($container) {
    return new FormController($container);
};

/**
 * Add Fieldsets Model to Flextype container
 */
$flextype['fieldsets'] = static function ($container) {
    return new Fieldsets($container);
};

/**
 * Add form twig extension
 */
$flextype->view->addExtension(new FormTwigExtension($flextype));
