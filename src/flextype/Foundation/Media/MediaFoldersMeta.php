<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation\Media;

use Atomastic\Macroable\Macroable;

class MediaFoldersMeta
{
    use Macroable;

    /**
     * Get files directory meta location
     *
     * @param string $id Unique identifier of the folder.
     *
     * @return string entry directory location
     *
     * @access public
     */
    public function getDirectoryMetaLocation(string $id): string
    {
        return PATH['project'] . '/uploads/.meta/' . $id;
    }
}
