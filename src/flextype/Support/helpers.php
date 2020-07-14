<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Support\Collection;

if (! function_exists('collect')) {
    /**
     * Create a collection from the given value.
     *
     * @param  array $value Items to collect
     */
    function collect($array) : \Flextype\Collection
    {
        return new Collection($array);
    }
}
