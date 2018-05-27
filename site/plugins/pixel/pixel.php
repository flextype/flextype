<?php

/**
 *
 * Flextype Pixel Plugin
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Flextype\Component\{Event\Event, Registry\Registry};

//
// Shortcode: [pixel text="test"]
//
Event::addListener('onShortcodesInitialized', function () {
    Content::shortcode()->addHandler('pixel', function(ShortcodeInterface $s) {
        return pixel($s->getParameter('text'),
                     $s->getParameter('width'),
                     $s->getParameter('category'),
                     $s->getParameter('gray')
                 );
    });
});

/**
 * Return Pixel Image
 *
 * @param  string  $text     Image text
 * @param  int     $width    Image width
 * @param  int     $height   Image height
 * @param  string  $category Image category
 * @param  bool    $gray     Image category
 * @return string
 */
function pixel(string $text, $width = null, $height = null, $category = null, bool $gray = true) : string
{
    (isset($gray) && $gray == true) and $gray = 'g/' or $gray   = '';
    (isset($width))    and $width    = $width.'/'    or $width    = Registry::get('plugins.pixel.width').'/';
    (isset($height))   and $height   = $height.'/'   or $height   = Registry::get('plugins.pixel.height').'/';
    (isset($category)) and $category = $category.'/' or $category = Registry::get('plugins.pixel.category').'/';
    (isset($text))     and $text     = $text.'/'     or $text = '';
    return rtrim('http://lorempixel.com/'.$gray.$width.$height.$category.$text, '/\\');
}
