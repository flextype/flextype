<?php

/**
 *
 * Flextype Sitemap Plugin
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\{Event\Event, Http\Http, Arr\Arr, Registry\Registry};

$segments   = Http::getUriSegments();
//$uri_string =  str_replace("/rss", "", Http::getUriString());
$rss_uri    = array_pop($segments);
//$page_uri   = array_pop($segments);

if ($rss_uri == 'rss') {
    Event::addListener('onShortcodesInitialized', function () {
        Http::setResponseStatus(200);
        Http::setRequestHeaders('Content-Type: text/xml; charset=utf-8');

        $_pages = Content::getPages(str_replace("/rss", "", Http::getUriString()), false, 'date');

        foreach ($_pages as $page) {
            if ($page['slug'] !== '404') {
                $pages[] = $page;
            }
        }

        Themes::view('feed/views/templates/rss')->display();

        Http::requestShutdown();
    });
}
