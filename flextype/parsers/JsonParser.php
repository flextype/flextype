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

class JsonParser
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
     * Set the maximum depth.
     *
     * @var int
     */
    public static $encode_depth = 512;

    /**
     * Decode assoc
     *
     * Set the maximum depth.
     *
     * @var int
     */
    public static $decode_assoc = true;

    /**
     * Decode Depth
     *
     * Set the maximum depth.
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
     * $result = JsonParser::encode($json_content);
     *
     * @param mixed $input          A string containing JSON
     * @param int   $encode_depth   User specified recursion depth.
     * @param int   $encode_options Bitmask consisting of encode options.
     *
     * @return mixed The JSON converted to a PHP value
     */
    public static function encode($input, int $encode_options = 0, int $encode_depth = 512) : string
    {
        $encoded = @json_encode(
            $input,
            $encode_options ? $encode_options : self::$encode_options,
            $encode_depth ? $encode_depth : self::$encode_depth
        );

        if ($encoded === false) {
            throw new RuntimeException('Encoding JSON failed');
        }

        return $encoded;
    }

    /**
     * Takes a JSON encoded string and converts it into a PHP variable.
     *
     * $array = JsonParser::decode($json_file_content);
     *
     * @param string $input          A string containing JSON
     * @param bool   $decode_assoc   When TRUE, returned objects will be converted into associative arrays.
     * @param int    $decode_depth   User specified recursion depth.
     * @param int    $decode_options Bitmask consisting of decode options.
     *
     * @return mixed The JSON converted to a PHP value
     *
     * @throws ParseException If the JSON is not valid
     */
    public static function decode(string $input, bool $decode_assoc = true, int $decode_depth = 512, int $decode_options = 0)
    {
        $decoded = @json_decode(
            $input,
            $decode_assoc ? $decode_assoc : self::$decode_assoc,
            $decode_depth ? $decode_depth : self::$decode_depth,
            $decode_options ? $decode_options : self::$decode_options
        );

        if ($decoded === false) {
            throw new RuntimeException('Decoding JSON failed');
        }

        return $decoded;
    }
}
