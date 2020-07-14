<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

class Container
{
    /**
     * Flextype Dependency Container
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
