<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

class MediaFoldersMeta
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
     * Get files directory meta location
     *
     * @param string $id Unique identifier of the folder.
     *
     * @return string entry directory location
     *
     * @access public
     */
    public function getDirMetaLocation(string $id) : string
    {
        return PATH['project'] . '/uploads/.meta/' . $id;
    }
}
