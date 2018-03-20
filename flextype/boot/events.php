<?php namespace Flextype;

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Set Flextype Meta Generator
Events::addListener('onThemeMeta', function () {
    echo('<meta name="generator" content="Powered by Flextype" />');
});
