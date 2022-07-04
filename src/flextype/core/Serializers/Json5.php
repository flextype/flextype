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

use RuntimeException;

use function cache;
use function defined;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function registry;
use function Glowy\Strings\strings;

use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class Json5
{
    public const FORCE_ARRAY    = 0b0001;
    public const PRETTY         = 0b0010;
    public const ESCAPE_UNICODE = 0b0100;

    /**
     * Returns the JSON5 representation of a value.
     *
     * @param mixed $input The PHP value.
     *
     * @return mixed A JSON5 string representing the original PHP value.
     */
    public function encode($input)
    {
        $options = registry()->get('flextype.settings.serializers.json5.encode.options');
        $depth   = registry()->get('flextype.settings.serializers.json5.encode.depth');

        $options = ($options & self::ESCAPE_UNICODE ? 0 : JSON_UNESCAPED_UNICODE)
            | JSON_UNESCAPED_SLASHES
            | ($options & self::PRETTY ? JSON_PRETTY_PRINT : 0)
            | (defined('JSON_PRESERVE_ZERO_FRACTION') ? JSON_PRESERVE_ZERO_FRACTION : 0);

        $json = json_encode($input, $options, $depth);

        if ($error = json_last_error()) {
            throw new RuntimeException(json_last_error_msg(), $error);
        }

        return $json;
    }

    /**
     * Takes a JSON5 encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing JSON5.
     *
     * @return mixed The JSON5 converted to a PHP value.
     *
     * @throws RuntimeException If the JSON5 is not valid.
     */
    public function decode(string $input)
    {
        $cache = registry()->get('flextype.settings.serializers.json5.decode.cache.enabeled');
        $assoc = registry()->get('flextype.settings.serializers.json5.decode.assoc');
        $depth = registry()->get('flextype.settings.serializers.json5.decode.depth');
        $flags = registry()->get('flextype.settings.serializers.json5.decode.flags');

        $decode = static function (string $input, bool $assoc, int $depth, int $flags) {
            return json5_decode($input, $assoc, $depth, $flags);;
        };

        if ($cache === true && registry()->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = cache()->get($key)) {
                return $dataFromCache;
            }

            $data = $decode($input, $assoc, $depth, $flags);
            cache()->set($key, $data);

            return $data;
        }

        return $decode($input, $assoc, $depth, $flags);
    }

    /**
     * Get Cache ID for JSON5.
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
        return strings('json5' . $input . $string . registry()->get('flextype.settings.serializers.json5.decode.cache.string'))->hash()->toString();
    }
}
