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

class EntriesTwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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
            'entries' => new EntriesTwig($this->flextype)
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
    public function fetch(string $entry)
    {
        return $this->flextype['entries']->fetch($entry);
    }

    /**
     * Fetch all entries
     */
    public function fetchAll(string $entry, string $order_by = 'date', string $order_type = 'DESC', int $offset = null, int $length = null,  bool $recursive = false) : array
    {
        return $this->flextype['entries']->fetchAll($entry, $order_by, $order_type, $offset, $length, $recursive);
    }
}
