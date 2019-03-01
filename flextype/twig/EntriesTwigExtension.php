<?php

namespace Flextype;

class EntriesTwigExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('entries_fetch', array($this, 'fetch')),
            new \Twig_SimpleFunction('entries_fetch_all', array($this, 'fetchAll')),
        ];
    }

    public function fetch(string $entry)
    {
        return $this->flextype['entries']->fetch($entry);
    }

    public function fetchAll(string $entry, string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null) : array
    {
        return $this->flextype['entries']->fetchAll($entry, $order_by, $order_type, $offset, $length);
    }
}
