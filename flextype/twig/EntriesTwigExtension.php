<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Twig-View
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/slimphp/Twig-View/blob/master/LICENSE.md (MIT License)
 */
namespace Flextype;

class EntriesTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('entries_fetch', array($this, 'fetch')),
            new \Twig_SimpleFunction('entries_fetch_all', array($this, 'fetchAll')),
        ];
    }

    public function fetch(string $entry)
    {
        return Entries::fetch($entry);
    }

    public function fetchAll(string $entry, string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null) : array
    {
        return Entries::fetchAll($entry, $order_by, $order_type, $offset, $length);
    }
}
