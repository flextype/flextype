<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use function md5;

class Parser
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Parsers
     *
     * @var array
     */
    private $parsers = [
        'frontmatter' => [
            'name' => 'frontmatter',
            'ext' => 'md',
        ], 'json' => [
            'name' => 'json',
            'ext' => 'json',
        ], 'yaml' => [
            'name' => 'yaml',
            'ext' => 'yaml',
        ],
    ];

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Get Parser Information
     *
     * @param string $input  Content to parse
     * @param string $parser Parser type [frontmatter, json, yaml]
     *
     * @return array
     */
    public function getParserInfo(string $parser) : array
    {
        return $this->parsers[$parser];
    }

    /**
     * Dumps a PHP value to a string CONTENT.
     *
     * @param string $input  Content to parse
     * @param string $parser Parser type [frontmatter, json, yaml]
     *
     * @return mixed PHP value converted to a string CONTENT.
     */
    public function encode(string $input, string $parser) : string
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
            
                break;
        }
    }

    /**
     * Parse INPUT content into a PHP value.
     *
     * @param string $input  Content to parse
     * @param string $parser Parser type [frontmatter, json, yaml]
     * @param bool   $cache  Cache result data or no. Default is true
     *
     * @return mixed The Content converted to a PHP value
     */
    public function decode(string $input, string $parser, bool $cache = true)
    {
        switch ($parser) {
            case 'frontmatter':
                if ($cache) {
                    $key = md5($input);

                    if ($data_from_cache = $this->flextype['cache']->fetch($key)) {
                        return $data_from_cache;
                    }

                    $data = FrontmatterParser::decode($input);
                    $this->flextype['cache']->save($key, $data);

                    return $data;
                } else {
                    return FrontmatterParser::decode($input);
                }

                break;
            case 'json':
                if ($cache) {
                    $key = md5($input);

                    if ($data_from_cache = $this->flextype['cache']->fetch($key)) {
                        return $data_from_cache;
                    }

                    $data = JsonParser::decode($input);
                    $this->flextype['cache']->save($key, $data);

                    return $data;
                } else {
                    return JsonParser::decode($input);
                }

                break;
            case 'yaml':
                if ($cache) {
                    $key = md5($input);

                    if ($data_from_cache = $this->flextype['cache']->fetch($key)) {
                        return $data_from_cache;
                    }

                    $data = YamlParser::decode($input);
                    $this->flextype['cache']->save($key, $data);

                    return $data;
                } else {
                    return YamlParser::decode($input);
                }

                break;
            default:
                // code...
                break;
        }
    }
}
