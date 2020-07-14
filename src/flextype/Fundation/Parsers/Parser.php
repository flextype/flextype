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
        'markdown' => [
            'name' => 'markdown',
            'ext' => 'md',
        ],
        'shortcodes' => [
            'name' => 'shortcodes',
            'ext' => 'php',
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
     * Parse INPUT content.
     *
     * @param string $input  Content to parse
     * @param string $parser Parser type [frontmatter, json, yaml, markdown]
     * @param bool   $cache  Cache result data or no. Default is true
     *
     * @return mixed The Content converted to a PHP value
     */
    public function parse(string $input, string $parser, bool $cache = true)
    {
        switch ($parser) {
            case 'markdown':
                if ($cache === true && $this->flextype['registry']->get('flextype.settings.cache.enabled') === true) {
                    $key = md5($input);

                    if ($data_from_cache = $this->flextype['cache']->fetch($key)) {
                        return $data_from_cache;
                    }

                    $data = Markdown::decode($input);
                    $this->flextype['cache']->save($key, $data);

                    return $data;
                } else {
                    return Markdown::decode($input);
                }

                break;
                case 'shortcodes':
                    if ($cache === true && $this->flextype['registry']->get('flextype.settings.cache.enabled') === true) {
                        $key = md5($input);

                        if ($data_from_cache = $this->flextype['cache']->fetch($key)) {
                            return $data_from_cache;
                        }

                        $data = $this->flextype['shortcodes']->process($input);
                        $this->flextype['cache']->save($key, $data);

                        return $data;
                    } else {
                        return $this->flextype['shortcodes']->process($input);
                    }

                break;
            default:
                // code...
                break;
        }
    }
}
