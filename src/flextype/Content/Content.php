<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Content;

use Atomastic\Macroable\Macroable;
use Flextype\Storage;

class Content extends Storage
{
    use Macroable;
}