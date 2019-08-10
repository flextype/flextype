<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained Flextype Community.
 */

namespace Flextype;

use const PHP_EOL;
use function array_slice;
use function count;
use function implode;
use function ltrim;
use function preg_split;
use function trim;

class FrontmatterParser
{
    /**
     * Front matter parser
     *
     * @param  string $content Content to parse
     *
     * @return array
     *
     * @access public
     */
    public static function frontMatterParser(string $content) : array
    {
        $parts = preg_split('/^[\s\r\n]?---[\s\r\n]?$/sm', PHP_EOL . ltrim($content));
        if (count($parts) < 3) {
            return ['content' => $content];
        }

        return YamlParser::decode(trim($parts[1])) + ['content' => implode(PHP_EOL . '---' . PHP_EOL, array_slice($parts, 2))];
    }

    public static function encode($input) : string
    {
        return '';
    }

    public static function decode(string $input)
    {
        return self::frontMatterParser($input);
    }
}
