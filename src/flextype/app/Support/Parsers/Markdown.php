<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Support\Parsers;

use function md5;

class Markdown
{
    /**
     * Markdown
     */
    protected $markdown;

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
     * Takes a MARKDOWN encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing MARKDOWN
     * @param bool   $cache Cache result data or no. Default is true
     *
     * @return mixed The MARKDOWN converted to a PHP value
     */
    public function parse(string $input, bool $cache = true) : string
    {
        if ($cache === true && flextype('registry')->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($data_from_cache = flextype('cache')->fetch($key)) {
                return $data_from_cache;
            }

            $data = $this->_parse($input);
            flextype('cache')->save($key, $data);

            return $data;
        }

        return $this->_parse($input);
    }

    /**
     * @see parse()
     */
    protected function _parse(string $input) : string
    {
        return $this->markdown->text($input);
    }

    protected function getCacheID($input)
    {
        return md5('markdown' . $input);
    }
}
