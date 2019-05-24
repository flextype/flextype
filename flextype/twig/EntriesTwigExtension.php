<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
            new \Twig_SimpleFunction('entries_fetch', [$this, 'fetch']),
            new \Twig_SimpleFunction('entries_fetch_all', [$this, 'fetchAll']),
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
