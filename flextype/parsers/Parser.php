<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

class Parser
{
    public static $drivers = [
        'json' => [
            'name' => 'json',
            'ext' => 'json',
        ], 'yaml' => [
            'name' => 'yaml',
            'ext' => 'yaml',
        ], 'frontmatter' => [
            'name' => 'frontmatter',
            'ext' => 'md',
        ],
    ];

    public static function encode($input, string $driver) : string
    {
        switch ($driver) {
            case 'json':
                return JsonParser::encode($input);

                break;
            case 'yaml':
                return YamlParser::encode($input);

                break;
            case 'frontmatter':
                return FrontmatterParser::encode($input);

                break;
            default:
                // code...
                break;
        }
    }

    public static function decode(string $input, string $driver)
    {
        switch ($driver) {
            case 'json':
                return JsonParser::decode($input);

                break;
            case 'yaml':
                return YamlParser::decode($input);

                break;
            case 'frontmatter':
                return FrontmatterParser::decode($input);

                break;
            default:
                // code...
                break;
        }
    }
}
