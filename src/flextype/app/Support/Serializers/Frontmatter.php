<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Support\Serializers;

use Flextype\Component\Arrays\Arrays;
use function array_slice;
use function count;
use function implode;
use function ltrim;
use function md5;
use function preg_replace;
use function preg_split;
use function trim;
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
    public function encode($input) : string
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

            if ($data_from_cache = flextype('cache')->fetch($key)) {
                return $data_from_cache;
            }

            $data = $this->_decode($input);
            flextype('cache')->save($key, $data);

            return $data;
        }

        return $this->_decode($input);
    }

    /**
     * @see encode()
     */
    protected function _encode($input) : string
    {
        if (isset($input['content'])) {
            $content = $input['content'];
            Arrays::delete($input, 'content');
            $matter = flextype('yaml')->encode($input);
        } else {
            $content = '';
            $matter  = flextype('yaml')->encode($input);
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
        $parts = preg_split('/^[\s\r\n]?---[\s\r\n]?$/sm', PHP_EOL . ltrim($input));

        if (count($parts) < 3) {
            return ['content' => trim($input)];
        }

        return flextype('yaml')->decode(trim($parts[1]), false) + ['content' => trim(implode(PHP_EOL . '---' . PHP_EOL, array_slice($parts, 2)))];
    }

    protected function getCacheID($input)
    {
        return md5('frontmatter' . $input);
    }
}
