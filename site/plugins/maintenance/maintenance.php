<?php

/**
 *
 * Flextype Maintenance Plugin
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\{Event\Event, Http\Http, Registry\Registry};

if (Registry::get('plugins.maintenance.activated')) {
    Event::addListener('onPageBeforeRender', function () {
        Http::setResponseStatus(503);
        Http::setRequestHeaders('Content-Type: text/html; charset=utf-8');
        Themes::view('maintenance/views/templates/maintenance')->display();
        Http::requestShutdown();
    });
}
