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
use Symfony\Component\Yaml\Exception\DumpException as SymfonyYamlDumpException;
use Symfony\Component\Yaml\Exception\ParseException as SymfonyYamlParseException;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;

use function error_reporting;
use function Flextype\cache;
use function Flextype\registry;
use function function_exists;
use function Glowy\Strings\strings;
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
     * Dumps a PHP value to a YAML string.
     *
     * The dump method, when supplied with an array, will do its best
     * to convert the array into friendly YAML.
     *
     * @param mixed $input The PHP value.
     *
     * @return string A YAML string representing the original PHP value.
     */
    public function encode(mixed $input): string
    {
        $inline = registry()->get('flextype.settings.serializers.yaml.encode.inline');
        $indent = registry()->get('flextype.settings.serializers.yaml.encode.indent');
        $flags  = registry()->get('flextype.settings.serializers.yaml.encode.flags');

        try {
            return SymfonyYaml::dump(
                $input,
                $inline,
                $indent,
                $flags
            );
        } catch (SymfonyYamlDumpException $e) {
            throw new RuntimeException('Encoding YAML failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Parses YAML into a PHP value.
     *
     * @param string $input A string containing YAML.
     *
     * @return mixed The YAML converted to a PHP value.
     *
     * @throws RuntimeException If the YAML is not valid.
     */
    public function decode(string $input): mixed
    {
        $cache  = registry()->get('flextype.settings.serializers.yaml.decode.cache.enabled');
        $flags  = registry()->get('flextype.settings.serializers.yaml.decode.flags');
        $native = registry()->get('flextype.settings.serializers.yaml.decode.native');

        $decode = static function (string $input, int $flags, bool $native) {
            // Try native PECL YAML PHP extension first if available.
            if (function_exists('yaml_parse') && $native === true) {
                // Safely decode YAML.

                // Save and Mute error_reporting
                $errorReporting = error_reporting();
                error_reporting(0);

                $saved = ini_get('yaml.decode_php');
                ini_set('yaml.decode_php', '0');
                $decoded = yaml_parse($input);
                ini_set('yaml.decode_php', $saved);

                // Restore error_reporting
                error_reporting($errorReporting);

                if ($decoded !== false) {
                    return $decoded;
                }
            }

            try {
                return SymfonyYaml::parse($input, $flags);
            } catch (SymfonyYamlParseException $e) {
                throw new RuntimeException('Decoding YAML failed: ' . $e->getMessage(), 0, $e);
            }
        };

        if ($cache === true && registry()->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = cache()->get($key)) {
                return $dataFromCache;
            }

            $data = $decode($input, $flags, $native);
            cache()->set($key, $data);

            return $data;
        }

        return $decode($input, $flags, $native);
    }

    /**
     * Get Cache ID for YAML.
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
        return strings('yaml' . $input . $string . registry()->get('flextype.settings.serializers.yaml.decode.cache.string'))->hash()->toString();
    }
}
