<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Symfony\Component\Finder\Finder;

if (! function_exists('find')) {
    /**
     * Create a Finder instance.
     */
    function find(): Finder
    {
        return new Finder();
    }
}
