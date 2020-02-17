<?php

declare(strict_types=1);

/**
 * @link http://digital.flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;

class IconController extends Controller
{
    public static function icon($value)
    {
        $icon_parts = explode(" ", $value);

        if ($icon_parts[0] == 'fas') {
            $icon_category = 'solid';
        } elseif ($icon_parts[0] == 'fab') {
            $icon_category = 'brands';
        } elseif ($icon_parts[0] == 'far') {
            $icon_category = 'regular';
        }

        $icon_name = str_replace("fa-", "", $icon_parts[1]);

        $icon = Filesystem::read(PATH['plugins'] . '/icon/assets/dist/fontawesome/svgs/' . $icon_category . '/' . $icon_name . '.svg');

        return $icon;
    }
}
