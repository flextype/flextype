<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation;

class Container
{
    /**
     * Dependency Container
     */
    protected $container;

    /**
     * __construct
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * __get
     */
    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }
}
