<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Parsers;

use Atomastic\Macroable\Macroable;
use Flextype\Support\Parsers\Shortcodes;

class Parsers
{
    use Macroable;

    /**
     * Create a Shortcodes instance.
     */
    public function shortcodes(): Shortcodes
    {
        return Shortcodes::getInstance();
    }
}
