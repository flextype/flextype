<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Twig_Extension;
use Twig_SimpleFunction;

class IconAdminTwigExtension extends Twig_Extension
{
    /**
     * Callback for twig.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('icon', [$this, 'icon'], ['is_safe' => ['html']])
        ];
    }

    public function icon($value)
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

        $icon = Filesystem::read(PATH['plugins'] . '/admin/assets/dist/fontawesome/svgs/' . $icon_category . '/' . $icon_name . '.svg');

        return $icon;
    }
}
