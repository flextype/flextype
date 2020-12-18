<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Serializers;

use function array_slice;
use function arrays;
use function count;
use function flextype;
use function implode;
use function ltrim;
use function preg_replace;
use function preg_split;
use function strings;

use const PHP_EOL;

class Frontmatter
{
    /**
     * Returns the FRONTMATTER representation of a value
     *
     * @param mixed $input The PHP value
     *
     * @return string A FRONTMATTER string representing the original PHP value
     */
    public function encode($input): string
    {
        return $this->_encode($input);
    }

    /**
     * Takes a FRONTMATTER encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing FRONTMATTER
     * @param bool   $cache Cache result data or no. Default is true
     *
     * @return mixed The FRONTMATTER converted to a PHP value
     */
    public function decode(string $input, bool $cache = true)
    {
        if ($cache === true && flextype('registry')->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = flextype('cache')->get($key)) {
                return $dataFromCache;
            }

            $data = $this->_decode($input);
            flextype('cache')->set($key, $data);

            return $data;
        }

        return $this->_decode($input);
    }

    /**
     * @see encode()
     */
    protected function _encode($input): string
    {
        if (isset($input['content'])) {
            $content = $input['content'];
            $input   = arrays($input)->delete('content')->toArray();
            $matter  = flextype('serializers')->yaml()->encode($input);
        } else {
            $content = '';
            $matter  = flextype('serializers')->yaml()->encode($input);
        }

        return '---' . "\n" .
                   $matter .
                   '---' . "\n" .
                   $content;
    }

    /**
     * @see decode()
     */
    protected function _decode(string $input)
    {
        // Remove UTF-8 BOM if it exists.
        $input = ltrim($input, "\xef\xbb\xbf");

        // Normalize line endings to Unix style.
        $input = (string) preg_replace("/(\r\n|\r)/", "\n", $input);

        // Parse Frontmatter and Body
        $parts = preg_split('/^[\s\r\n]?---[\s\r\n]?$/sm', PHP_EOL . strings($input)->trimLeft()->toString());

        if (count($parts) < 3) {
            return ['content' => strings($input)->trim()->toString()];
        }

        return flextype('serializers')->yaml()->decode(strings($parts[1])->trim()->toString(), false) + ['content' => strings(implode(PHP_EOL . '---' . PHP_EOL, array_slice($parts, 2)))->trim()->toString()];
    }

    public function getCacheID($input): string
    {
        return strings('frontmatter' . $input)->hash()->toString();
    }
}
