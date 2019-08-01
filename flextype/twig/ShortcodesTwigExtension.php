<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Maintained by Sergey Romanenko and Flextype Community.
 *
 * @license https://github.com/flextype/flextype/blob/master/LICENSE.txt (MIT License)
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFilter;

class ShortcodesTwigExtension extends Twig_Extension
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
     * Returns a list of filters to add to the existing list.
     *
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new Twig_SimpleFilter('shortcode', [$this, 'shortcode']),
        ];
    }

    /**
     * Shorcode process
     */
    public function shortcode(string $value) : string
    {
        return $this->flextype->shortcodes->process($value);
    }
}
