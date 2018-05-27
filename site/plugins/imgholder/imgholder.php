<?php

/**
 *
 * Flextype Imgholder Plugin
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
// Shortcode: [imgholder text="test"]
//
Event::addListener('onShortcodesInitialized', function () {
    Content::shortcode()->addHandler('imgholder', function(ShortcodeInterface $s) {
        return imgholder($s->getParameter('text'),
                         $s->getParameter('width'),
                         $s->getParameter('height'),
                         $s->getParameter('bg_color'),
                         $s->getParameter('text_color'),
                         $s->getParameter('ext'),
                         $s->getParameter('font_name'),
                         $s->getParameter('font_size')
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
function imgholder(string $text = null, int $width = null, int $height = null, string $bg_color = null, string $text_color = null, string $ext = null, string $font_name = null, string $font_size = null) : string
{
    (isset($width))      and $width      = $width.'x'      or $width      = Registry::get('plugins.imgholder.width').'x';
    (isset($height))     and $height     = $height.'/'     or $height     = Registry::get('plugins.imgholder.height').'/';
    (isset($bg_color))   and $bg_color   = $bg_color.'/'   or $bg_color   = Registry::get('plugins.imgholder.bg_color').'/';
    (isset($text_color)) and $text_color = $text_color     or $text_color = Registry::get('plugins.imgholder.text_color');
    (isset($ext))        and $ext        = '.'.$ext        or $ext        = '.'.Registry::get('plugins.imgholder.ext');
    (isset($text))       and $text       = '&text='.$text  or $text       = '&text='.Registry::get('plugins.imgholder.text');
    (isset($font_name))  and $font_name  = '&font='.$font_name  or $font_name  = '&font='.Registry::get('plugins.imgholder.font_name');
    (isset($font_size))  and $font_size  = '&fz='.$font_size    or $font_size  = '&fz='.Registry::get('plugins.imgholder.font_size');
    return rtrim('https://imgholder.ru/'.$width.$height.$bg_color.$text_color.$ext.$text.$font_name.$font_size, '/\\');
}
