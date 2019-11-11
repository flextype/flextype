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

class Frontmatter
{
    /**
     * Returns the Frontmatter representation of a value
     *
     * @param mixed $input The PHP value
     *
     * @return string A Frontmatter string representing the original PHP value
     */
    public static function encode($input) : string
    {
        if (isset($input['content'])) {
            $content = $input['content'];
            Arr::delete($input, 'content');
            $matter = Yaml::encode($input);
        } else {
            $content = '';
            $matter  = Yaml::encode($input);
        }

        $encoded = '---' . "\n" .
                   $matter .
                   '---' . "\n" .
                   $content;

        return $encoded;
    }

    /**
     * Takes a Frontmatter encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing Frontmatter
     *
     * @return mixed The Frontmatter converted to a PHP value
     */
    public static function decode(string $input)
    {
        $parts = preg_split('/^[\s\r\n]?---[\s\r\n]?$/sm', PHP_EOL . ltrim($input));
        if (count($parts) < 3) {
            return ['content' => trim($input)];
        }

        return Yaml::decode(trim($parts[1])) + ['content' => trim(implode(PHP_EOL . '---' . PHP_EOL, array_slice($parts, 2)))];
    }
}
