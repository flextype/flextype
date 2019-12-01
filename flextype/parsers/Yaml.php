<?php

declare(strict_types=1);

/**
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use RuntimeException;
use Symfony\Component\Yaml\Exception\DumpException as SymfonyYamlDumpException;
use Symfony\Component\Yaml\Exception\ParseException as SymfonyYamlParseException;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use function function_exists;
use function ini_get;
use function ini_set;

class Yaml
{
    public const DUMP_OBJECT                     = 1;
    public const PARSE_EXCEPTION_ON_INVALID_TYPE = 2;
    public const PARSE_OBJECT                    = 4;
    public const PARSE_OBJECT_FOR_MAP            = 8;
    public const DUMP_EXCEPTION_ON_INVALID_TYPE  = 16;
    public const PARSE_DATETIME                  = 32;
    public const DUMP_OBJECT_AS_MAP              = 64;
    public const DUMP_MULTI_LINE_LITERAL_BLOCK   = 128;
    public const PARSE_CONSTANT                  = 256;
    public const PARSE_CUSTOM_TAGS               = 512;
    public const DUMP_EMPTY_ARRAY_AS_SEQUENCE    = 1024;

    /**
     * Inline
     *
     * The level where you switch to inline YAML
     *
     * @var int
     */
    public static $inline = 5;

    /**
     * Ident
     *
     * The amount of spaces to use for indentation of nested nodes
     *
     * @var int
     */
    public static $indent = 2;

    /**
     * Native
     *
     * Use native parser or symfony parser
     *
     * @var bool
     */
    public static $native = true;

    /**
     * Flags
     *
     * A bit field of PARSE_* constants to customize the YAML parser behavior
     *
     * @var int
     */
    public static $flags = 16;

    /**
     * Dumps a PHP value to a YAML string.
     *
     * The dump method, when supplied with an array, will do its best
     * to convert the array into friendly YAML.
     *
     * @param mixed $input The PHP value
     *
     * @return string A YAML string representing the original PHP value
     */
    public static function encode($input) : string
    {
        try {
            return SymfonyYaml::dump(
                $input,
                self::$inline,
                self::$indent,
                self::$flags
            );
        } catch (SymfonyYamlDumpException $e) {
            throw new RuntimeException('Encoding YAML failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Parses YAML into a PHP value.
     *
     * @param string $input A string containing YAML
     *
     * @return array The YAML converted to a PHP value
     *
     * @throws ParseException If the YAML is not valid
     */
    public static function decode(string $input) : array
    {
        // Try native PECL YAML PHP extension first if available.
        if (\function_exists('yaml_parse') && self::$native) {
            // Safely decode YAML.
            $saved = @ini_get('yaml.decode_php');
            @ini_set('yaml.decode_php', '0');
            $decoded = @yaml_parse($input);
            @ini_set('yaml.decode_php', $saved);

            if ($decoded !== false) {
                return $decoded;
            }
        }

        try {
            return SymfonyYaml::parse($input, self::$flags);
        } catch (SymfonyYamlParseException $e) {
            throw new RuntimeException('Decoding YAML failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
