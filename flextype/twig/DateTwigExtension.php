<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFunction;
use function strlen;

class DateTwigExtension extends Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('dateformatToMomentJS', [$this, 'dateformatToMomentJS']),
        ];
    }

    public function dateformatToMomentJS($php_format)
    {
        $symbols_matching = [
            // Day
            'd' => 'DD',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'E',
            'S' => 'Do',
            'w' => 'd',
            'z' => 'DDD',
            // Week
            'W' => 'W',
            // Month
            'F' => 'MMMM',
            'm' => 'MM',
            'M' => 'MMM',
            'n' => 'M',
            't' => '',
            // Year
            'L' => '',
            'o' => 'GGGG',
            'Y' => 'YYYY',
            'y' => 'yy',
            // Time
            'a' => 'a',
            'A' => 'A',
            'B' => 'SSS',
            'g' => 'h',
            'G' => 'H',
            'h' => 'hh',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => '',
            // Timezone
            'e' => '',
            'I' => '',
            'O' => 'ZZ',
            'P' => 'Z',
            'T' => 'z',
            'Z' => '',
            // Full Date/Time
            'c' => '',
            'r' => 'llll ZZ',
            'U' => 'X',
        ];
        $js_format        = '';
        $escaping         = false;
        $len              = strlen($php_format);
        for ($i = 0; $i < $len; $i++) {
            $char = $php_format[$i];
            if ($char === '\\') { // PHP date format escaping character
                $i++;
                if ($escaping) {
                    $js_format .= $php_format[$i];
                } else {
                    $js_format .= '\'' . $php_format[$i];
                }

                $escaping = true;
            } else {
                if ($escaping) {
                    $js_format .= "'";
                    $escaping   = false;
                }

                if (isset($symbols_matching[$char])) {
                    $js_format .= $symbols_matching[$char];
                } else {
                    $js_format .= $char;
                }
            }
        }

        return $js_format;
    }
}
