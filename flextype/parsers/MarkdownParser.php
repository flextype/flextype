<?php

declare(strict_types=1);

/**
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use ParsedownExtra;

class MarkdownParser
{

    /**
     * Markdown Object
     *
     * @var object
     * @access private
     */
    private static $markdown = null;

    /**
     * parse
     */
    public static function parse($input) : string
    {
        !isset(self::$markdown) and self::$markdown = new ParsedownExtra();

        return self::$markdown->text($input);
    }
}
