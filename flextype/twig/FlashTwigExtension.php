<?php

namespace Flextype;

use Slim\Flash\Messages;

class FlashTwigExtension extends \Twig_Extension
{

    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Constructor.
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
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('flash', [$this, 'getMessages']),
        ];
    }

    /**
     * Returns Flash messages; If key is provided then returns messages
     * for that key.
     *
     * @param string $key
     * @return array
     */
    public function getMessages($key = null) : array
    {
        if (null !== $key) {
            return $this->flextype['flash']->getMessage($key);
        }
        return $this->flextype['flash']->getMessages();
    }
}
