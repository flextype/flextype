<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\App\Foundation\Flextype;

if (! function_exists('flextype')) {
    /**
     * Get the available Flextype Application instance
     * or try to get Dependency Injection Container if $container is not null.
     */
    function flextype($container_name = null, $container = [])
    {
        if (is_null($container_name)) {
            return Flextype::getInstance($container);
        }

        return Flextype::getInstance($container)->container($container_name);
    }
}
