<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Maintained by Sergey Romanenko and Flextype Community.
 *
 * @license https://github.com/flextype/flextype/blob/master/LICENSE.txt (MIT License)
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
            'emmiter' => new EmitterTwig($this->flextype),
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
    public function emmit($event)
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
