<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Tokens;

use Atomastic\Macroable\Macroable;
use Flextype\Entries;
use Exception;

class Tokens extends Entries
{
    use Macroable;

    /** 
     * Generate unique Token ID.
     *
     * @return string Token ID.
     *
     * @access public
     */
    public function generateID(): string
    {
        return bin2hex(random_bytes(16));
    }
}