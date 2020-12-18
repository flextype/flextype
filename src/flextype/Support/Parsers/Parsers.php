<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Parsers;

use Atomastic\Macroable\Macroable;

class Parsers
{
    use Macroable;

    /**
     * Create a Markdown instance.
     */
    public function markdown(): Markdown
    {
        return Markdown::getInstance();
    }

    /**
     * Create a Shortcode instance.
     */
    public function shortcode(): Shortcode
    {
        return Shortcode::getInstance();
    }
}
