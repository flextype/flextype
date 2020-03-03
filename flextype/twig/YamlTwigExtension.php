<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class YamlTwigExtension extends Twig_Extension
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
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('yaml_decode', [$this, 'decode']),
            new Twig_SimpleFunction('yaml_encode', [$this, 'encode']),
        ];
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new Twig_SimpleFilter('yaml_decode', [$this, 'decode']),
            new Twig_SimpleFilter('yaml_encode', [$this, 'encode']),
        ];
    }

    /**
     * Encode YAML
     */
    public function encode($input) : string
    {
        return $this->flextype['parser']->encode($input, 'yaml');
    }

    /**
     * Decode YAML
     */
    public function decode(string $input, bool $cache = true)
    {
        return $this->flextype['parser']->decode($input, 'yaml', $cache);
    }
}
