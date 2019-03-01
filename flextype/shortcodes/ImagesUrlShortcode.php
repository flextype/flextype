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

use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Images
// Shortcode: [images_url path="home/image.jpg"]
// Result: Display image url
$flextype['shortcodes']->addHandler('images_url', function(ShortcodeInterface $s) {

    $params = [];

    // API
    // http://glide.thephpleague.com/1.0/api/quick-reference/
    ($s->getParameter('or')) and $params['or'] = $s->getParameter('or');
    ($s->getParameter('flip')) and $params['flip'] = $s->getParameter('flip');
    ($s->getParameter('crop')) and $params['crop'] = $s->getParameter('crop');
    ($s->getParameter('w')) and $params['w'] = $s->getParameter('w');
    ($s->getParameter('h')) and $params['h'] = $s->getParameter('h');
    ($s->getParameter('fit')) and $params['fit'] = $s->getParameter('fit');
    ($s->getParameter('dpr')) and $params['dpr'] = $s->getParameter('dpr');
    ($s->getParameter('bri')) and $params['bri'] = $s->getParameter('bri');
    ($s->getParameter('con')) and $params['con'] = $s->getParameter('con');
    ($s->getParameter('gam')) and $params['gam'] = $s->getParameter('gam');
    ($s->getParameter('sharp')) and $params['sharp'] = $s->getParameter('sharp');
    ($s->getParameter('blur')) and $params['blur'] = $s->getParameter('blur');
    ($s->getParameter('pixel')) and $params['pixel'] = $s->getParameter('pixel');
    ($s->getParameter('filt')) and $params['filt'] = $s->getParameter('filt');
    ($s->getParameter('mark')) and $params['mark'] = $s->getParameter('mark');
    ($s->getParameter('markw')) and $params['markw'] = $s->getParameter('markw');
    ($s->getParameter('markh')) and $params['markh'] = $s->getParameter('markh');
    ($s->getParameter('markx')) and $params['markx'] = $s->getParameter('markx');
    ($s->getParameter('marky')) and $params['marky'] = $s->getParameter('marky');
    ($s->getParameter('markpad')) and $params['markpad'] = $s->getParameter('markpad');
    ($s->getParameter('markpos')) and $params['markpos'] = $s->getParameter('markpos');
    ($s->getParameter('markalpha')) and $params['markalpha'] = $s->getParameter('markalpha');
    ($s->getParameter('bg')) and $params['bg'] = $s->getParameter('bg');
    ($s->getParameter('border')) and $params['border'] = $s->getParameter('border');
    ($s->getParameter('q')) and $params['q'] = $s->getParameter('q');
    ($s->getParameter('fm')) and $params['fm'] = $s->getParameter('fm');

    return Http::getBaseUrl() . '/site/cache/glide/' . $flextype['images']->makeImage($s->getParameter('path'), $params);
});
