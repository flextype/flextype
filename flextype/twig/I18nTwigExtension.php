<?php

namespace Flextype;

use Flextype\Component\I18n\I18n;

class I18nTwigExtension extends \Twig_Extension
{
    /**
     * Callback for twig.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('tr', array($this, 'tr')),
        ];
    }

    public function tr(string $translate, string $locale = null, array $values = []) : string
    {
        return I18n::find($translate, $locale, $values);
    }
}
