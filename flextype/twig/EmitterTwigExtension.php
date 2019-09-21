<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_Extension_GlobalsInterface;

class EmitterTwigExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Constructor
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Register Global variables in an extension
     */
    public function getGlobals()
    {
        return [
            'emitter' => new EmitterTwig($this->flextype),
        ];
    }
}

class EmitterTwig
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Constructor
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Emitting event
     */
    public function emit($event)
    {
        return $this->flextype['emitter']->emit($event);
    }

    /**
     * Emitting events in batches
     */
    public function emitBatch(array $events)
    {
        return $this->flextype['emitter']->emitBatch($events);
    }
}
