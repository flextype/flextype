<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

class EmitterTwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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
            'emmiter' => new EmitterTwig($this->flextype)
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
