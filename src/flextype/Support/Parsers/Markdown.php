<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Parsers;

use function flextype;
use function strings;

class Markdown
{
    /**
     * Markdown
     */
    private $markdown;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($markdown)
    {
        $this->markdown = $markdown;
    }

    /**
     * Get Markdown instance
     *
     * @access public
     */
    public function getInstance()
    {
        return $this->markdown;
    }

    /**
     * Takes a MARKDOWN encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing MARKDOWN
     * @param bool   $cache Cache result data or no. Default is true
     *
     * @return mixed The MARKDOWN converted to a PHP value
     */
    public function parse(string $input, bool $cache = true): string
    {
        if ($cache === true && flextype('registry')->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = flextype('cache')->get($key)) {
                return $dataFromCache;
            }

            $data = $this->_parse($input);
            flextype('cache')->set($key, $data);

            return $data;
        }

        return $this->_parse($input);
    }

    /**
     * @see parse()
     */
    protected function _parse(string $input): string
    {
        return $this->markdown->text($input);
    }

    public function getCacheID($input): string
    {
        return strings('markdown' . $input)->hash()->toString();
    }
}
