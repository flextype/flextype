<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

class Parser
{
    /**
     * Default parser
     *
     * @var array
     */
    public static $default_parser = 'frontmatter';

    /**
     * Parsers
     *
     * @var array
     */
    public static $parsers = [
        'frontmatter' => [
            'name' => 'frontmatter',
            'ext' => 'md',
        ],
        'json' => [
            'name' => 'json',
            'ext' => 'json',
        ], 'yaml' => [
            'name' => 'yaml',
            'ext' => 'yaml',
        ],
    ];

    public static function encode($input, string $parser) : string
    {
        switch ($parser) {
            case 'frontmatter':
                return FrontmatterParser::encode($input);

                break;
            case 'json':
                return JsonParser::encode($input);

                break;
            case 'yaml':
                return YamlParser::encode($input);

                break;
            default:
                // code...
                break;
        }
    }

    public static function decode(string $input, string $parser)
    {
        switch ($parser) {
            case 'frontmatter':
                return FrontmatterParser::decode($input);

                break;
            case 'json':
                return JsonParser::decode($input);

                break;
            case 'yaml':
                return YamlParser::decode($input);

                break;
            default:
                // code...
                break;
        }
    }
}
