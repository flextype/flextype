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
            new Twig_SimpleFunction('parser_decode', [$this, 'decode']),
            new Twig_SimpleFunction('parser_encode', [$this, 'encode']),
            new Twig_SimpleFunction('parser_get_info', [$this, 'getParserInfo']),
        ];
    }

    /**
     * Get Parser Info
     */
    public function getParserInfo(string $parser) : array
    {
        return $this->flextype['parser']->getParserInfo($parser);
    }

    /**
     * Encode
     */
    public function encode($input, string $parser)
    {
        return $this->flextype['parser']->encode($input, $parser);
    }

    /**
     * Decode
     */
    public function decode(string $input, string $parser, bool $cache = true)
    {
        return $this->flextype['parser']->decode($input, $parser, $cache);
    }
}
