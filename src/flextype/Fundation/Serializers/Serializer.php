<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use function md5;

class Serializer
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Serializer
     *
     * @var array
     */
    private $serializer = [
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
     * @param string $input      Content to parse
     * @param string $serializer Parser type [frontmatter, json, yaml]
     *
     * @return array
     */
    public function getParserInfo(string $serializer) : array
    {
        return $this->serializer[$serializer];
    }

    /**
     * Dumps a PHP value to a string CONTENT.
     *
     * @param mixed  $input      Content to parse
     * @param string $serializer Parser type [frontmatter, json, yaml]
     *
     * @return mixed PHP value converted to a string CONTENT.
     */
    public function encode($input, string $serializer) : string
    {
        switch ($serializer) {
            case 'frontmatter':
                return Frontmatter::encode($input);

                break;
            case 'json':
                return Json::encode($input);

                break;
            case 'yaml':
                return Yaml::encode($input);

                break;
            default:
                break;
        }
    }

    /**
     * Parse INPUT content into a PHP value.
     *
     * @param string $input      Content to parse
     * @param string $serializer Serializer type [frontmatter, json, yaml]
     * @param bool   $cache      Cache result data or no. Default is true
     *
     * @return mixed The Content converted to a PHP value
     */
    public function decode(string $input, string $serializer, bool $cache = true)
    {
        switch ($serializer) {
            case 'frontmatter':
                if ($cache === true && $this->flextype['registry']->get('flextype.settings.cache.enabled') === true) {
                    $key = md5($input);

                    if ($data_from_cache = $this->flextype['cache']->fetch($key)) {
                        return $data_from_cache;
                    }

                    $data = Frontmatter::decode($input);
                    $this->flextype['cache']->save($key, $data);

                    return $data;
                } else {
                    return Frontmatter::decode($input);
                }

                break;
            case 'json':
                if ($cache === true && $this->flextype['registry']->get('flextype.settings.cache.enabled') === true) {
                    $key = md5($input);

                    if ($data_from_cache = $this->flextype['cache']->fetch($key)) {
                        return $data_from_cache;
                    }

                    $data = Json::decode($input);
                    $this->flextype['cache']->save($key, $data);

                    return $data;
                } else {
                    return Json::decode($input);
                }

                break;
            case 'yaml':
                if ($cache === true && $this->flextype['registry']->get('flextype.settings.cache.enabled') === true) {
                    $key = md5($input);

                    if ($data_from_cache = $this->flextype['cache']->fetch($key)) {
                        return $data_from_cache;
                    }

                    $data = Yaml::decode($input);
                    $this->flextype['cache']->save($key, $data);

                    return $data;
                } else {
                    return Yaml::decode($input);
                }

                break;
            default:
                // code...
                break;
        }
    }
}
