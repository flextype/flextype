<?php

declare(strict_types=1);

/**
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFunction;

class FlashTwigExtension extends Twig_Extension
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
            new Twig_SimpleFunction('flash', [$this, 'getMessages']),
        ];
    }

    /**
     * Returns Flash messages; If key is provided then returns messages
     * for that key.
     *
     * @return array
     */
    public function getMessages(?string $key = null) : array
    {
        if ($key !== null) {
            return $this->flextype['flash']->getMessage($key);
        }

        return $this->flextype['flash']->getMessages();
    }
}
