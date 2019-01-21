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

class FrontmatterParser {

    /**
     * Get [matter] and [body] from a content.
     * PHP implementation of Jekyll Front Matter.
     *
     * $content = Entries::frontMatterParser($content);
     *
     * @param  string $content Content to parse
     * @access public
     * @return array
     */
    public static function parse(string $content) : array
    {
       $parts = preg_split('/^[\s\r\n]?---[\s\r\n]?$/sm', PHP_EOL.ltrim($content));

       if (count($parts) < 3) return ['matter' => [], 'body' => $content];

       return ['matter' => trim($parts[1]), 'body' => implode(PHP_EOL.'---'.PHP_EOL, array_slice($parts, 2))];
    }
}
