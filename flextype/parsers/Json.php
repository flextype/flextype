<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use RuntimeException;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use function json_decode;
use function json_encode;

class Json
{
    /**
     * Encode options
     *
     * Bitmask consisting of encode options
     * https://www.php.net/manual/en/function.json-encode.php
     *
     * @var int
     */
    public static $encode_options = JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT;

    /**
     * Encode Depth
     *
     * Set the maximum depth. Must be greater than zero.
     *
     * @var int
     */
    public static $encode_depth = 512;

    /**
     * Decode assoc
     *
     * When TRUE, returned objects will be converted into associative arrays.
     *
     * @var int
     */
    public static $decode_assoc = true;

    /**
     * Decode Depth
     *
     * User specified recursion depth.
     *
     * @var int
     */
    public static $decode_depth = 512;

    /**
     * Decode options
     *
     * Bitmask consisting of decode options
     * https://www.php.net/manual/en/function.json-decode.php
     *
     * @var int
     */
    public static $decode_options = 0;

    /**
     * Returns the JSON representation of a value
     *
     * @param mixed $input The PHP value
     *
     * @return mixed A JSON string representing the original PHP value
     */
    public static function encode($input) : string
    {
        $encoded = @json_encode(
            $input,
            self::$encode_options,
            self::$encode_depth
        );

        if ($encoded === false) {
            throw new RuntimeException('Encoding JSON failed');
        }

        return $encoded;
    }

    /**
     * Takes a JSON encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing JSON
     *
     * @return mixed The JSON converted to a PHP value
     *
     * @throws ParseException If the JSON is not valid
     */
    public static function decode(string $input)
    {
        $decoded = @json_decode(
            $input,
            self::$decode_assoc,
            self::$decode_depth,
            self::$decode_options
        );

        if ($decoded === false) {
            throw new RuntimeException('Decoding JSON failed');
        }

        return $decoded;
    }
}
