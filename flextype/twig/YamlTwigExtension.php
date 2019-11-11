<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFunction;

class YamlTwigExtension extends Twig_Extension
{
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
     * Encode YAML
     */
    public function encode($input) : string
    {
        return Yaml::encode($input);
    }

    /**
     * Decode YAML
     */
    public function decode(string $input)
    {
        return Yaml::decode($input);
    }
}
