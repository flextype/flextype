<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Serializers;

use RuntimeException;

use function defined;
use function flextype;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function strings;

use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class Json
{
    public const FORCE_ARRAY    = 0b0001;
    public const PRETTY         = 0b0010;
    public const ESCAPE_UNICODE = 0b0100;

    /**
     * Returns the JSON representation of a value
     *
     * @param mixed $input   The PHP value
     * @param int   $options Bitmask consisting of encode options
     * @param int   $depth   Encode Depth. Set the maximum depth. Must be greater than zero.
     *
     * @return mixed A JSON string representing the original PHP value
     */
    public function encode($input, int $options = 0, int $depth = 512): string
    {
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
     * Takes a JSON encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing JSON
     * @param bool   $cache Cache result data or no. Default is true
     * @param bool   $assoc Decode assoc. When TRUE, returned objects will be converted into associative arrays.
     * @param int    $depth Decode Depth. Set the maximum depth. Must be greater than zero.
     * @param int    $flags Bitmask consisting of decode options
     *
     * @return mixed The JSON converted to a PHP value
     *
     * @throws ParseException If the JSON is not valid
     */
    public function decode(string $input, bool $cache = true, bool $assoc = true, int $depth = 512, int $flags = 0)
    {
        $decode = function (string $input, bool $assoc = true, int $depth = 512, int $flags = 0) {
            $value = json_decode($input, $assoc, $depth, $flags);

            if ($error = json_last_error()) {
                throw new RuntimeException(json_last_error_msg(), $error);
            }

            return $value;
        };

        if ($cache === true && flextype('registry')->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = flextype('cache')->get($key)) {
                return $dataFromCache;
            }

            $data = $decode($input, $assoc, $depth, $flags);
            flextype('cache')->set($key, $data);

            return $data;
        }

        return $decode($input);
    }

    /**
     * Get Cache ID for JSON.
     *
     * @param  string $input Input.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $input): string
    {
        return strings('json' . $input)->hash()->toString();
    }
}
