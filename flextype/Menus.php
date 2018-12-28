<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;

class Menus
{
    /**
     * Get menu
     *
     * Menu::get('menu-name');
     *
     * @access public
     * @param  string  $menu_name  Menu name
     * @return array
     */
    public static function get(string $menu_name)
    {
        $menu_path = PATH['menus'] . '/' . $menu_name . '.yaml';

        if (Filesystem::fileExists($menu_path)) {
            return YamlParser::decode(Filesystem::getFileContent($menu_path));
        } else {
           throw new \RuntimeException("Menu {$menu_name} does not exist.");
       }
    }
}
