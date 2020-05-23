<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use function md5;

class Files
{
    /**
     * Flextype Dependency Container
     *
     * @access private
     */
    private $flextype;

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
     * Create file
     *
     * @param string $data     Unique identifier of the entry(entries).
     * @return array The entry array data.
     *
     * @access public
     */
    public function create(string $data) : array
    {

    }
}
