<?php namespace Rawilum;

/**
 * @package Rawilum
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://rawilum.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Set Rawilum Meta Generator
Events::addListener('onThemeMeta', function () {
    echo('<meta name="generator" content="Powered by Rawilum" />');
});
