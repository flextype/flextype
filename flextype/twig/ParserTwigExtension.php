<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFunction;

class ParserTwigExtension extends Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('parser_decode', [$this, 'decode']),
            new Twig_SimpleFunction('parser_encode', [$this, 'encode']),
        ];
    }

    /**
     * Encode YAML
     */
    public function encode($input, string $parser)
    {
        return Parser::encode($input, $parser);
    }

    /**
     * Decode YAML
     */
    public function decode(string $input, string $parser)
    {
        return Parser::decode($input, $parser);
    }
}
