<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS
 * and with the full functionality of a traditional CMS!
 *
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype\Serializers;

use function array_filter;
use function array_values;
use function Flextype\cache;
use function Flextype\collection;
use function Flextype\registry;
use function Flextype\serializers;
use function Glowy\Strings\strings;
use function in_array;
use function is_array;
use function ltrim;
use function preg_replace;
use function preg_split;

use const PHP_EOL;

class Frontmatter
{
    /**
     * Returns the FRONTMATTER representation of a value.
     *
     * @param mixed $input The PHP value.
     *
     * @return string A FRONTMATTER string representing the original PHP value.
     */
    public function encode(mixed $input): string
    {
        $headerSerializer = registry()->get('flextype.settings.serializers.frontmatter.encode.header.serializer');
        $allowed          = registry()->get('flextype.settings.serializers.frontmatter.encode.header.allowed');

        if ($headerSerializer === 'frontmatter') {
            $headerSerializer = 'yaml';
        }

        if (! in_array($headerSerializer, $allowed)) {
            $headerSerializer = 'yaml';
        }

        if (isset($input['content'])) {
            $content = $input['content'];
            $input   = collection($input)->delete('content')->toArray();
            $matter  = serializers()->{$headerSerializer}()->encode($input);
        } else {
            $content = '';
            $matter  = serializers()->{$headerSerializer}()->encode($input);
        }

        return '---' . "\n" . $matter . '---' . "\n" . $content;
    }

    /**
     * Takes a FRONTMATTER encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing FRONTMATTER.
     *
     * @return mixed The FRONTMATTER converted to a PHP value.
     */
    public function decode(string $input): mixed
    {
        $cache            = registry()->get('flextype.settings.serializers.frontmatter.decode.cache.enabled');
        $headerSerializer = registry()->get('flextype.settings.serializers.frontmatter.decode.header.serializer');
        $allowed          = registry()->get('flextype.settings.serializers.frontmatter.encode.header.allowed');

        if ($headerSerializer === 'frontmatter') {
            $headerSerializer = 'yaml';
        }

        if (! in_array($headerSerializer, $allowed)) {
            $headerSerializer = 'yaml';
        }

        $decode = static function (string $input) use ($headerSerializer, $allowed) {
            // Remove UTF-8 BOM if it exists.
            $input = ltrim($input, "\xef\xbb\xbf");

            // Normalize line endings to Unix style.
            $input = (string) preg_replace("/(\r\n|\r)/", "\n", $input);

            // Parse Frontmatter and Body
            $parts = preg_split('/^[\s\r\n]?---[\s\r\n]?$/sm', PHP_EOL . strings($input)->trimLeft()->toString());

            // Replace empty array item with empty string and reindex array.
            if (empty($parts[0])) {
                unset($parts[0]);
                $parts = array_values(array_filter($parts));
            }

            // Check for custom frontmatter header serializers
            if (strings(strings($parts[0])->lines()[1])->trim()->contains('---')) {
                $headerSerializer = strings(strings($parts[0])->lines()[1])->trim()->after('---')->toString();

                $parts[0] = strings($parts[0])->replaceFirst('---' . $headerSerializer, '')->toString();

                if (! in_array($headerSerializer, $allowed)) {
                    $headerSerializer = 'yaml';
                }
            }

            $frontmatter = serializers()->{$headerSerializer}()->decode(strings($parts[0])->trim()->toString(), false);
            $content     = ['content' => strings($parts[1] ?? '')->trim()->toString()];

            return (is_array($frontmatter) ? $frontmatter : []) + $content;
        };

        if ($cache === true && registry()->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = cache()->get($key)) {
                return $dataFromCache;
            }

            $data = $decode($input);
            cache()->set($key, $data);

            return $data;
        }

        return $decode($input);
    }

    /**
     * Get Cache ID for frontmatter.
     *
     * @param  string $input  Input.
     * @param  string $string String to append to the Cache ID.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $input, string $string = ''): string
    {
        return strings('frontmatter' . $input . $string . registry()->get('flextype.settings.serializers.frontmatter.decode.cache.string'))->hash()->toString();
    }
}
