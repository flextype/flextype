<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Foundation\Actions;

if (! function_exists('actions')) {
    /**
     * Get the available Flextype instance.
     */
    function actions()
    {
        return Actions::getInstance();
    }
}
