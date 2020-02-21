<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFunction;

class SiteUrlTwigExtension extends Twig_Extension
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
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('site_url', [$this, 'site_url'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Get Site URL
     */
    public function site_url() : string
    {
        return $this->flextype->SiteController->getSiteUrl();
    }
}
