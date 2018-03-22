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

class Templates
{

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        // Nothing here
    }

    /**
     * Get Themes template
     *
     * @access public
     * @param  string $template_name Template name
     * @return mixed
     */
    public static function display(string $template_name)
    {
        $template_ext = '.php';

        $page = Pages::$page;

        $template_path = THEMES_PATH . '/' . Config::get('site.theme') . '/' . $template_name . $template_ext;

        if (Flextype::filesystem()->exists($template_path)) {
            include $template_path;
        } else {
            throw new RuntimeException("Template {$template_name} does not exist.");
        }
    }
}
