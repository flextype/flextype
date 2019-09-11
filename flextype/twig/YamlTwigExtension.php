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
    public function encode($input, int $inline = 5, int $indent = 2, int $flags = 16) : string
    {
        return YamlParser::encode($input, $inline, $indent, $flags);
    }

    /**
     * Decode YAML
     */
    public function decode(string $input, int $flags = 0)
    {
        return YamlParser::decode($input, $flags);
    }
}
