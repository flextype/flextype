<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Foundation\Flextype;

if (! function_exists('flextype')) {
    /**
     * Get the available Flextype Application instance
     * or try to get Dependency Injection Container if $container is not null.
     */
    function flextype($containerName = null, $container = [])
    {
        if (is_null($containerName)) {
            return Flextype::getInstance($container);
        }

        return Flextype::getInstance($container)->container($containerName);
    }
}
