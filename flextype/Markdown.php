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

use ParsedownExtra;

class Markdown
{
    /**
     * Parsedown Extra Object
     *
     * @var object
     * @access protected
     */
    protected static $markdown;

    /**
     * Markdown parser
     *
     * @access  public
     * @param  string $content Content to parse
     * @return string Formatted content
     */
    public static function parse(string $content) : string
    {
        !static::$markdown and static::$markdown = new ParsedownExtra();

        return static::$markdown->text($content);
    }
}
