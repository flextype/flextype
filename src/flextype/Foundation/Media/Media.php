<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation\Media;

use Atomastic\Macroable\Macroable;

class Media
{
    use Macroable;

    /**
     * Create a Media Files instance.
     */
    public function files(): MediaFiles
    {
        return new MediaFiles();
    }

    /**
     * Create a Media Files instance.
     */
    public function folders(): MediaFolders
    {
        return new MediaFolders();
    }
}
