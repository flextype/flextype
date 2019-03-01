<?php

namespace Flextype;

class EmitterTwigExtension extends \Twig_Extension
{

    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * __construct
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('emmiter_emmit', array($this, 'emit')),
        ];
    }

    public function emit(string $event)
    {
        return $this->flextype['emitter']->emit($event);
    }
}
