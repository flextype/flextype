<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation;

use Slim\App;

class Flextype extends App
{
    /**
     * Flextype version
     *
     * @var string
     */
    const FLEXTYPE_VERSION = '0.9.0';

    private $instance;

    private $flextype;

    public function __construct($flextype = [])
    {
        parent::__construct($flextype);
        $this->instance  = $this;
        $this->container = $this->instance->getContainer();
    }

    public function container($key = null)
    {
        if ($key != null) {
            return $this->container[$key];
        }

        return $this->container;
    }

    public function getInstance()
    {
        return $this->instance;
    }
}
