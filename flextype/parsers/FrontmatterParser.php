<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
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
    public static function parser(string $content) : array
    {
        $parts = preg_split('/^[\s\r\n]?---[\s\r\n]?$/sm', PHP_EOL . ltrim($content));
        if (count($parts) < 3) {
            return ['content' => trim($content)];
        }

        return YamlParser::decode(trim($parts[1])) + ['content' => trim(implode(PHP_EOL . '---' . PHP_EOL, array_slice($parts, 2)))];
    }

    public static function encode($input) : string
    {
        if (isset($input['content'])) {
            $content = $input['content'];
            Arr::delete($input, 'content');
            $matter = YamlParser::encode($input);
        } else {
            $content = '';
            $matter  = YamlParser::encode($input);
        }

        $encoded = '---' . "\n" .
                   $matter .
                   '---' . "\n" .
                   $content;

        return $encoded;
    }

    public static function decode(string $input)
    {
        return self::parser($input);
    }
}
