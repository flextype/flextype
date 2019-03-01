<?php

namespace Flextype;

class EmitterTwigExtension extends \Twig_Extension
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
     * Callback for twig.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('emmiter_emmit', array($this, 'emit')),
        ];
    }

    public function emit(string $event)
    {
        $this->flextype['emitter']->emit($event);
    }
}
