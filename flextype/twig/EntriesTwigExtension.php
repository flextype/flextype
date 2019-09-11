<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_Extension_GlobalsInterface;

class EntriesTwigExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
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
            'entries' => new EntriesTwig($this->flextype),
        ];
    }
}

class EntriesTwig
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
     * Fetch single entry
     */
    public function fetch(string $id)
    {
        return $this->flextype['entries']->fetch($id);
    }

    /**
     * Fetch all entries
     */
    public function fetchAll(string $id, array $args = []) : array
    {
        return $this->flextype['entries']->fetchAll($id, $args);
    }
}
