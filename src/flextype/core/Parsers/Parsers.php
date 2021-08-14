<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers;

use Atomastic\Macroable\Macroable;

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

    /**
     * Create a Markdown instance.
     */
    public function markdown(): Markdown
    {
        return Markdown::getInstance();
    }
}
