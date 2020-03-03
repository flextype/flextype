<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\I18n\I18n;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class I18nTwigExtension extends Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('tr', [$this, 'tr']),
        ];
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new Twig_SimpleFilter('tr', [$this, 'tr']),
        ];
    }

    /**
     * Translate string
     */
    public function tr(string $translate, array $values = [], ?string $locale = null) : string
    {
        return I18n::find($translate, $values, $locale);
    }
}
