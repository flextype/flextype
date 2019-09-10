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
use Symfony\Component\Yaml\Exception\DumpException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use function function_exists;
use function ini_get;
use function ini_set;

class YamlParser
{
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
     * Use native parser or symfony
     *
     * @var bool
     */
    public static $native = false;

    /**
     * Flag
     *
     * A bit field of PARSE_* constants to customize the YAML parser behavior
     *
     * @var int
     */
    public static $flag = 16;

    /**
     * Dumps a PHP value to a YAML string.
     *
     * The dump method, when supplied with an array, will do its best
     * to convert the array into friendly YAML.
     *
     * @param mixed $input  The PHP value
     * @param int   $inline The level where you switch to inline YAML
     * @param int   $indent The amount of spaces to use for indentation of nested nodes
     * @param int   $flags  A bit field of DUMP_* constants to customize the dumped YAML string
     *
     * @return string A YAML string representing the original PHP value
     */
    public static function encode($input, int $inline = 5, int $indent = 2, int $flags = 16) : string
    {
        try {
            return Yaml::dump(
                $input,
                $inline ? $inline : self::$inline,
                $indent ? $indent : self::$indent,
                $flags  ? $flags  : self::$flag
            );
        } catch (DumpException $e) {
            throw new RuntimeException('Encoding YAML failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Parses YAML into a PHP value.
     *
     * $array = YamlParser::decode($yaml_file_content);
     *
     * @param string $input A string containing YAML
     * @param int    $flags A bit field of PARSE_* constants to customize the YAML parser behavior
     *
     * @return mixed The YAML converted to a PHP value
     *
     * @throws ParseException If the YAML is not valid
     */
    public static function decode(string $input, int $flags = 0)
    {
        // Try native PECL YAML PHP extension first if available.
        if (self::$native && function_exists('yaml_parse')) {
            // Safely decode YAML.
            $saved = @ini_get('yaml.decode_php');
            @ini_set('yaml.decode_php', 0);
            $decoded = @yaml_parse($input);
            @ini_set('yaml.decode_php', $saved);

            if ($decoded !== false) {
                return (array) $decoded;
            }
        }

        try {
            return (array) Yaml::parse($input);
        } catch (ParseException $e) {
            throw new RuntimeException('Decoding YAML failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
