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

class Icon extends Model
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
        } else {
            $icon_category = 'regular';
        }

        $icon_name = str_replace("fa-", "", $icon_parts[1]);

        $icon_file_path = PATH['plugins'] . '/icon/assets/dist/fontawesome/svgs/' . $icon_category . '/' . $icon_name . '.svg';
        $icon_fallback_file_path = PATH['plugins'] . '/icon/assets/dist/fontawesome/svgs/regular/file-alt.svg';

        if (Filesystem::has($icon_file_path)) {
            $icon = Filesystem::read($icon_file_path);
        } else {
            $icon = Filesystem::read($icon_fallback_file_path);
        }

        return $icon;
    }
}
