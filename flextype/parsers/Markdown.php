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

class Markdown
{
    /**
     * Markdown Object
     *
     * @var object
     * @access private
     */
    private static $markdown = null;

    /**
     * Takes a MARKDOWN encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing MARKDOWN
     *
     * @return mixed The MARKDOWN converted to a PHP value
     */
    public static function decode(string $input) : string
    {
        ! isset(self::$markdown) and self::$markdown = new ParsedownExtra();

        return self::$markdown->text($input);
    }
}
