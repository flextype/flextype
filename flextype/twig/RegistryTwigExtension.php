<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Twig-View
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/slimphp/Twig-View/blob/master/LICENSE.md (MIT License)
 */
namespace Flextype;

class RegistryTwigExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('registry_get', array($this, 'get')),
            new \Twig_SimpleFunction('registry_exists', array($this, 'exists')),
        ];
    }

    public function get($name)
    {
        return Registry::get($name);
    }

    public function exists(string $name) : bool
    {
        return Registry::exists($name);
    }
}
