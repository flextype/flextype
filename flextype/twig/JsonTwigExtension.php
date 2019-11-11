<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFunction;

class JsonTwigExtension extends Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('json_decode', [$this, 'decode']),
            new Twig_SimpleFunction('json_encode', [$this, 'encode']),
        ];
    }

    /**
     * Encode JSON
     */
    public function encode($input) : string
    {
        return Json::encode($input);
    }

    /**
     * Decode JSON
     */
    public function decode(string $input)
    {
        return Json::decode($input);
    }
}
